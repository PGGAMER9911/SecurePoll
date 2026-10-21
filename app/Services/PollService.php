<?php
namespace app\Services;

use app\Models\Poll;
use app\Models\PollOption;
use app\Models\Vote;
use app\Models\EligibleVoter;
use app\Models\AuditLog;
use app\Helpers\Security;
use app\Services\NotificationService;

class PollService {
    private $pollModel;
    private $optionModel;
    private $voteModel;
    private $eligibleVoterModel;
    private $auditLogger;
    private $db;
    private $notificationService;

    public function __construct() {
        $this->pollModel = new Poll();
        $this->optionModel = new PollOption();
        $this->voteModel = new Vote();
        $this->eligibleVoterModel = new EligibleVoter();
        $this->auditLogger = new AuditLog();
        $this->db = \app\Core\Database::getInstance()->getConnection();
        $this->notificationService = new NotificationService();
    }

    public static function getLifecycleStatus($poll) {
        if (in_array($poll['status'], ['Draft', 'Paused', 'Ended', 'Closed', 'Archived'], true)) {
            return $poll['status'];
        }

        $now = new \DateTime();
        $start = !empty($poll['start_time']) ? new \DateTime($poll['start_time']) : null;
        $end = !empty($poll['end_time']) ? new \DateTime($poll['end_time']) : null;

        if ($start && $now < $start) {
            return 'Upcoming';
        }

        if ($end && $now >= $end) {
            return 'Closed';
        }

        return 'Active';
    }

    public function createPoll($userId, $data, $options, $eligibleVoters = []) {
        // Check for exact duplicate poll (same creator, same title, same options irrespective of order)
        $existingPolls = $this->pollModel->getByCreatorAndTitle($userId, trim($data['title']));
        if (!empty($existingPolls)) {
            // Normalize incoming options
            $normalizedIncomingOptions = array_map(function($o) { return trim(strtolower($o)); }, $options);
            // Sort to make order-insensitive
            sort($normalizedIncomingOptions);
            
            foreach ($existingPolls as $ep) {
                $existingOptionsData = $this->optionModel->getByPollId($ep['id']);
                if (count($existingOptionsData) === count($normalizedIncomingOptions)) {
                    $normalizedExistingOptions = array_map(function($o) { return trim(strtolower($o['option_text'])); }, $existingOptionsData);
                    sort($normalizedExistingOptions);
                    
                    if ($normalizedIncomingOptions === $normalizedExistingOptions) {
                        return ['success' => false, 'message' => 'You’ve already created this poll.'];
                    }
                }
            }
        }

        $pollData = [
            'creator_id' => $userId,
            'title' => Security::sanitize($data['title']),
            'description' => Security::sanitize($data['description'] ?? ''),
            'category' => Security::sanitize($data['category'] ?? ''),
            'banner_image' => Security::sanitize($data['banner_image'] ?? ''),
            'rules' => Security::sanitize($data['rules'] ?? ''),
            'language' => Security::sanitize($data['language'] ?? 'en'),
            'visibility' => $data['visibility'] ?? 'Public',
            'voting_type' => $data['voting_type'] ?? 'Single',
            'is_anonymous' => isset($data['is_anonymous']) ? (int)$data['is_anonymous'] : 1,
            'result_visibility' => $data['result_visibility'] ?? 'Live',
            'max_voters' => !empty($data['max_voters']) ? (int)$data['max_voters'] : null,
            'min_required_votes' => !empty($data['min_required_votes']) ? (int)$data['min_required_votes'] : 0,
            'start_time' => !empty($data['start_time']) ? Security::sanitize($data['start_time']) : date('Y-m-d H:i:s'),
            'end_time' => !empty($data['end_time']) ? Security::sanitize($data['end_time']) : null,
            'auto_close' => isset($data['auto_close']) ? (int)$data['auto_close'] : 0,
            'status' => $data['status'] ?? 'Active'
        ];
        
        if ($pollData['start_time'] > date('Y-m-d H:i:s')) {
            $pollData['status'] = 'Scheduled';
        } elseif (isset($data['status']) && $data['status'] === 'Draft') {
            $pollData['status'] = 'Draft';
        } else {
            $pollData['status'] = 'Active';
        }

        $pollId = $this->pollModel->create($pollData);

        if (!$pollId) {
            return ['success' => false, 'message' => 'Failed to create poll.'];
        }

        // Add Options
        foreach ($options as $index => $optText) {
            if (!empty(trim($optText))) {
                $this->optionModel->create([
                    'poll_id' => $pollId,
                    'option_text' => Security::sanitize($optText),
                    'sort_order' => $index
                ]);
            }
        }

        // Add Eligible Voters for Private Polls
        if ($pollData['visibility'] === 'Private' && !empty($eligibleVoters)) {
            $emails = explode(',', $eligibleVoters);
            foreach($emails as $email) {
                $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->eligibleVoterModel->add([
                        'poll_id' => $pollId,
                        'email' => $email
                    ]);
                }
            }
        }

        $this->auditLogger->log($userId, 'POLL_CREATED', 'polls', $pollId);
        
        $this->notificationService->notifyPollCreated($userId, $pollId, $pollData['title']);

        return ['success' => true, 'poll_id' => $pollId, 'message' => 'Poll created successfully.'];
    }

    public function updatePoll($pollId, $userId, $data, $incomingOptions) {
        $poll = $this->pollModel->getById($pollId);
        if (!$poll || $poll['creator_id'] != $userId) {
            return ['success' => false, 'message' => 'Unauthorized or poll not found.'];
        }
        
        try {
            $this->db->beginTransaction();
            
            $hasVotes = $this->voteModel->anyVotesExistForUpdate($pollId);
            
            if ($hasVotes) {
                // If attempting to change locked fields, return error
                if (isset($data['voting_type']) && $data['voting_type'] !== $poll['voting_type']) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Voting configuration cannot be changed after voting has started.'];
                }
                if (isset($data['is_anonymous']) && (int)$data['is_anonymous'] !== (int)$poll['is_anonymous']) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Voting configuration cannot be changed after voting has started.'];
                }
                
                // Options locking check
                $existingOptions = $this->optionModel->getByPollId($pollId);
                $existingTexts = array_map(function($o) { return trim(strtolower($o['option_text'])); }, $existingOptions);
                $incomingTexts = array_map(function($o) { return trim(strtolower(is_array($o) ? ($o['option_text'] ?? '') : $o)); }, $incomingOptions);
                
                sort($existingTexts);
                sort($incomingTexts);
                
                if ($existingTexts !== $incomingTexts) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Voting configuration cannot be changed after voting has started.'];
                }
                
                unset($data['voting_type']);
                unset($data['is_anonymous']);
            }
            
            $updateData = [];
            if (isset($data['title'])) $updateData['title'] = Security::sanitize($data['title']);
            if (isset($data['description'])) $updateData['description'] = Security::sanitize($data['description']);
            if (isset($data['visibility'])) $updateData['visibility'] = Security::sanitize($data['visibility']);
            if (isset($data['max_voters'])) $updateData['max_voters'] = $data['max_voters'] === '' ? null : (int)$data['max_voters'];
            
            if (array_key_exists('start_time', $data)) {
                $updateData['start_time'] = empty($data['start_time']) ? null : Security::sanitize($data['start_time']);
            }
            if (array_key_exists('end_time', $data)) {
                $updateData['end_time'] = empty($data['end_time']) ? null : Security::sanitize($data['end_time']);
            }
            
            if (!$hasVotes) {
                if (isset($data['voting_type'])) $updateData['voting_type'] = Security::sanitize($data['voting_type']);
                if (isset($data['is_anonymous'])) $updateData['is_anonymous'] = (int)$data['is_anonymous'];
            }
            
            if (!empty($updateData)) {
                $this->pollModel->update($pollId, $updateData);
            }
            
            if (!$hasVotes) {
                $existingOptions = $this->optionModel->getByPollId($pollId);
                
                $incomingTexts = [];
                $idx = 0;
                foreach ($incomingOptions as $opt) {
                    $text = trim(is_array($opt) ? ($opt['option_text'] ?? '') : $opt);
                    if (!empty($text)) {
                        $incomingTexts[] = ['text' => $text, 'sort_order' => $idx++];
                    }
                }
                
                $existingCount = count($existingOptions);
                $incomingCount = count($incomingTexts);
                
                for ($i = 0; $i < max($existingCount, $incomingCount); $i++) {
                    if ($i < $existingCount && $i < $incomingCount) {
                        $this->optionModel->updateOption($existingOptions[$i]['id'], Security::sanitize($incomingTexts[$i]['text']), $incomingTexts[$i]['sort_order']);
                    } elseif ($i < $incomingCount) {
                        $this->optionModel->create([
                            'poll_id' => $pollId,
                            'option_text' => Security::sanitize($incomingTexts[$i]['text']),
                            'sort_order' => $incomingTexts[$i]['sort_order']
                        ]);
                    } elseif ($i < $existingCount) {
                        $this->optionModel->deleteOption($existingOptions[$i]['id']);
                    }
                }
            }
            
            $this->auditLogger->log($userId, 'POLL_UPDATED', 'polls', $pollId);
            $this->db->commit();
            return ['success' => true, 'message' => 'Poll updated successfully.'];
            
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Poll update failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating the poll.'];
        }
    }

    public function castVote($pollId, $optionIds, $userId, $userEmail, $ipAddress) {
        if (!is_array($optionIds)) {
            $optionIds = [$optionIds];
        }
        
        $cleanOptionIds = [];
        foreach ($optionIds as $opt) {
            if (is_numeric($opt) && (int)$opt > 0) {
                $cleanOptionIds[] = (int)$opt;
            }
        }

        if (empty($cleanOptionIds)) {
            return ['success' => false, 'message' => 'No options selected.'];
        }

        if (count($cleanOptionIds) !== count(array_unique($cleanOptionIds))) {
            return ['success' => false, 'message' => 'Duplicate options selected.'];
        }

        $optionIds = array_values(array_unique($cleanOptionIds));

        $poll = $this->pollModel->getById($pollId);
        if (!$poll) {
            return ['success' => false, 'message' => 'Poll not found.'];
        }
        
        if (count($optionIds) > 1 && $poll['voting_type'] !== 'Multiple') {
            return ['success' => false, 'message' => 'This poll only allows a single choice.'];
        }
        
        // Creator restriction check
        if ($userId !== null && $userId == $poll['creator_id']) {
            return ['success' => false, 'message' => 'You cannot vote on your own poll.'];
        }
        
        // Lifecycle Check
        $currentStatus = self::getLifecycleStatus($poll);
        
        if ($currentStatus === 'Upcoming') {
            return ['success' => false, 'message' => 'Voting for this poll has not started yet.'];
        }
        
        if ($currentStatus === 'Closed' || $currentStatus === 'Ended') {
            if ($poll['auto_close'] && $poll['status'] !== 'Ended' && $poll['status'] !== 'Closed') {
                $this->pollModel->updateStatus($pollId, 'Ended');
            }
            return ['success' => false, 'message' => 'Voting for this poll has ended.'];
        }

        if ($currentStatus === 'Paused') {
            return ['success' => false, 'message' => 'This poll is currently paused.'];
        }

        if ($currentStatus === 'Archived') {
            return ['success' => false, 'message' => 'This poll has been archived.'];
        }
        
        if ($currentStatus !== 'Active') {
            return ['success' => false, 'message' => 'This poll is currently not active.'];
        }

        if (!empty($poll['max_voters'])) {
            $totalVotes = $this->voteModel->getTotalVotes($pollId);
            if ($totalVotes >= $poll['max_voters']) {
                return ['success' => false, 'message' => 'This poll has reached its maximum number of voters.'];
            }
        }

        if ($poll['visibility'] === 'Private') {
            $invModel = new \app\Models\PollInvitation();
            $authorizedInv = null;
            
            if (!$userEmail && $userId) {
                $userRec = (new \app\Models\User())->findById($userId);
                $userEmail = $userRec['email'] ?? null;
            }

            // 1. Try to authorize via accepted invitation in session
            if ($userEmail) {
                $sessionInv = $invModel->getByPollAndEmail($pollId, $userEmail);
                if ($sessionInv && $sessionInv['status'] === 'Accepted') {
                    $authorizedInv = $sessionInv;
                }
            }
            
            // 2. If not authorized via session, try token (for initial pending invite flow)
            if (!$authorizedInv) {
                $invitationToken = $_POST['invitation_token'] ?? $_POST['token'] ?? null;
                if (!$invitationToken) {
                    return ['success' => false, 'message' => 'An accepted invitation or token is required for this private poll.'];
                }
                
                $inv = $invModel->getByToken($invitationToken);
                
                if (!$inv || $inv['poll_id'] != $pollId) {
                    return ['success' => false, 'message' => 'Invalid invitation token.'];
                }
                
                if (!empty($inv['expires_at']) && date('Y-m-d H:i:s') > $inv['expires_at']) {
                    return ['success' => false, 'message' => 'This invitation token has expired.'];
                }
                
                if ($inv['status'] === 'Declined' || $inv['status'] === 'Revoked') {
                    return ['success' => false, 'message' => 'This invitation is no longer valid.'];
                }

                if ($inv['status'] === 'Accepted' && (!$userEmail || $inv['email'] !== $userEmail)) {
                    return ['success' => false, 'message' => 'This invitation token has already been used.'];
                }

                $authorizedInv = $inv;
            }
            
            $inv = $authorizedInv;
        }

        if ($poll['is_anonymous']) {
            if (!isset($_COOKIE['sp_device_token'])) {
                $deviceToken = bin2hex(random_bytes(32));
                $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
                if (!headers_sent()) {
                    setcookie('sp_device_token', $deviceToken, [
                        'expires' => time() + (10 * 365 * 24 * 60 * 60),
                        'path' => '/',
                        'domain' => '',
                        'secure' => $isHttps,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }
                $_COOKIE['sp_device_token'] = $deviceToken;
            } else {
                $deviceToken = $_COOKIE['sp_device_token'];
            }
            
            $voterHash = $this->computeVoterHash($pollId, true, $userId, $deviceToken);
            $storedUserId = null;
        } else {
            if (!$userId) {
                return ['success' => false, 'message' => 'This poll requires you to be logged in (not anonymous).'];
            }
            $voterHash = $this->computeVoterHash($pollId, false, $userId);
            $storedUserId = $userId;
        }

        // Application-level lock to prevent concurrent requests bypassing hasVoted check
        $lockName = 'poll_vote_' . $pollId . '_' . md5($voterHash);
        $stmtLock = $this->db->prepare("SELECT GET_LOCK(:lock, 3)");
        $stmtLock->execute(['lock' => $lockName]);
        $lockAcquired = $stmtLock->fetchColumn();

        if (!$lockAcquired) {
            return ['success' => false, 'message' => 'System is processing your previous request. Please try again.'];
        }

        $this->db->beginTransaction();

        try {
            if ($this->voteModel->hasVoted($pollId, $voterHash)) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'You have already voted on this poll.'];
            }

            if ($storedUserId && $this->voteModel->hasVotedByUserId($pollId, $storedUserId)) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'You have already voted on this poll.'];
            }

            // Verify all options belong to this poll
            $pollOptions = $this->optionModel->getByPollId($pollId);
            $validOptionIds = array_map('intval', array_column($pollOptions, 'id'));
            foreach ($optionIds as $optId) {
                if (!in_array((int)$optId, $validOptionIds, true)) {
                    $this->db->rollBack();
                    $optRec = $this->optionModel->getById((int)$optId);
                    if ($optRec) {
                        return ['success' => false, 'message' => 'Invalid option selected for this poll.'];
                    }
                    return ['success' => false, 'message' => 'Invalid option selected.'];
                }
            }

            $lastResult = null;
            foreach ($optionIds as $optId) {
                $voteData = [
                    'poll_id' => $pollId,
                    'option_id' => $optId,
                    'user_id' => $storedUserId,
                    'voter_hash' => $voterHash,
                    'ip_address' => $ipAddress
                ];

                $result = $this->voteModel->create($voteData);
                if ($result === 'DUPLICATE') {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Duplicate vote detected.'];
                } elseif (!$result) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to cast vote.'];
                }
                $lastResult = $result;
            }

            $logUserId = $poll['is_anonymous'] ? null : $userId;
            $this->auditLogger->log($logUserId, 'VOTE_CAST', 'polls', $pollId, null, null, $ipAddress);
            
            $this->notificationService->notifyVoteReceived($poll['creator_id'], $pollId, $poll['title']);
            
            if ($poll['visibility'] === 'Private') {
                $invModel = new \app\Models\PollInvitation();
                if ($inv && $inv['status'] !== 'Accepted') {
                    if (!$invModel->markAsUsed($inv['id'])) {
                        throw new \Exception("Failed to mark invitation as used.");
                    }
                    $this->notificationService->notify($poll['creator_id'], \app\Constants\NotificationTypes::INVITATION_ACCEPTED, 'Invitation Accepted', "Someone accepted your invitation to '{$poll['title']}'.", [
                        'category' => 'Invitations', 'entity_type' => 'poll', 'entity_id' => $pollId, 'icon' => 'user-check'
                    ]);
                }
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Vote cast successfully.'];

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Transaction failed during vote casting: " . $e->getMessage());
            return ['success' => false, 'message' => 'A system error occurred while casting your vote.'];
        } finally {
            $stmtRelease = $this->db->prepare("SELECT RELEASE_LOCK(:lock)");
            $stmtRelease->execute(['lock' => $lockName]);
        }
    }
    
    public function getPollWithDetails($pollId) {
        $poll = $this->pollModel->getById($pollId);
        if (!$poll) return null;
        
        $poll['options'] = $this->optionModel->getByPollId($pollId);
        $poll['total_votes'] = $this->voteModel->getTotalVotes($pollId);
        
        $results = $this->voteModel->getResultsByPoll($pollId);
        foreach($poll['options'] as &$opt) {
            $opt['votes'] = $results[$opt['id']] ?? 0;
        }
        
        return $poll;
    }
    
    public function getResults($pollId) {
        return $this->voteModel->getResultsByPoll($pollId);
    }

    public function getVoterActivity($pollId, $page = 1, $limit = 50) {
        $poll = $this->pollModel->getById($pollId);
        if (!$poll) {
            return ['success' => false, 'message' => 'Poll not found.'];
        }

        $offset = ($page - 1) * $limit;
        
        // Use the newly created getVoterActivity from Vote model
        $rawActivity = $this->voteModel->getVoterActivity($pollId, $limit, $offset);
        
        $isAnonymous = (bool)$poll['is_anonymous'];
        $mappedActivity = [];
        
        foreach ($rawActivity as $row) {
            $entry = [
                'vote_time' => $row['vote_time'],
                'selected_options' => explode('||', $row['selected_options'])
            ];
            
            if ($isAnonymous) {
                $entry['name'] = 'Anonymous Voter';
                $entry['voter_user_id'] = null;
            } else {
                $entry['name'] = $row['name'] ?: 'Unknown User';
                $entry['voter_user_id'] = $row['voter_user_id'];
            }
            // `email` and `voter_hash` are intentionally NOT included in $entry to prevent PII/correlation exposure.
            
            $mappedActivity[] = $entry;
        }
        
        return [
            'success' => true,
            'data' => $mappedActivity,
            'is_anonymous' => $isAnonymous
        ];
    }
    
    public function importCsv($pollId, $csvFile, $userId) {
        $poll = $this->pollModel->getById($pollId);
        if (!$poll || $poll['creator_id'] != $userId) {
            return ['success' => false, 'message' => 'Unauthorized or Poll not found.'];
        }
        
        if (!file_exists($csvFile) || !is_readable($csvFile)) {
            return ['success' => false, 'message' => 'CSV file cannot be read.'];
        }

        $handle = fopen($csvFile, "r");
        $header = fgetcsv($handle, 1000, ",");
        
        // Strip BOM if present
        $bom = pack('H*','EFBBBF');
        if (isset($header[0]) && strncmp($header[0], $bom, 3) === 0) {
            $header[0] = substr($header[0], 3);
        }
        
        if (!$header || count($header) < 4) {
            fclose($handle);
            return ['success' => false, 'message' => 'Invalid CSV structure. Expected columns: Name, Email, Department, Year.'];
        }
        
        $parsedHeader = array_map(function($h) { return trim(strtolower($h)); }, $header);
        if ($parsedHeader[0] !== 'name' || $parsedHeader[1] !== 'email' || 
            $parsedHeader[2] !== 'department' || $parsedHeader[3] !== 'year') {
            fclose($handle);
            return ['success' => false, 'message' => 'Invalid CSV headers. Expected exactly: Name, Email, Department, Year.'];
        }
        
        $importedCount = 0;
        $duplicatesCount = 0;
        $invalidCount = 0;
        $errors = [];
        $maxReportedErrors = 50;
        
        $invitationModel = new \app\Models\PollInvitation();
        $emailService = new \app\Services\EmailService();
        
        $maxRows = \app\Helpers\Env::get('CSV_MAX_ROWS', 1000);
        $rowCount = 0;

        $this->db->beginTransaction();
        
        try {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowCount++;
                if ($rowCount > $maxRows) {
                    throw new \Exception("Row limit of $maxRows exceeded. Import aborted.");
                }
                
                // Skip completely empty rows
                if (empty(array_filter($data, 'trim'))) {
                    continue;
                }

                // Expected format: name,email,department,year
                $name = \app\Helpers\Security::sanitize($data[0] ?? '');
                $rawEmail = trim($data[1] ?? '');
                $department = \app\Helpers\Security::sanitize($data[2] ?? '');
                $year = \app\Helpers\Security::sanitize($data[3] ?? '');
                
                if (empty($rawEmail) || !filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
                    $invalidCount++;
                    if (count($errors) < $maxReportedErrors) {
                        $errors[] = "Row " . ($rowCount + 1) . ": Invalid or missing email address.";
                    }
                    continue;
                }
                
                // Safe email
                $email = strtolower($rawEmail);

                if (!$this->eligibleVoterModel->isEligible($pollId, $email)) {
                    $this->eligibleVoterModel->add([
                        'poll_id' => $pollId,
                        'email' => $email,
                        'name' => $name,
                        'department' => $department,
                        'year_level' => $year
                    ]);
                    
                    // Generate Invitation Token
                    $token = bin2hex(random_bytes(16));
                    $invitationModel->create([
                        'poll_id' => $pollId,
                        'email' => $email,
                        'invitation_token' => $token
                    ]);
                    
                    $importedCount++;
                } else {
                    $duplicatesCount++;
                }
            }
            fclose($handle);
            
            $this->auditLogger->log($userId, 'CSV_IMPORT', 'polls', $pollId);
            $this->db->commit();
            
            $msg = "Successfully imported $importedCount voters. $duplicatesCount duplicates skipped.";
            if ($invalidCount > 0) {
                $msg .= " $invalidCount invalid rows rejected.";
            }
            
            return [
                'success' => true,
                'message' => $msg,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function deletePoll($id, $userId) {
        $poll = $this->pollModel->getById($id);
        if (!$poll || $poll['creator_id'] != $userId) {
            return false;
        }
        return $this->pollModel->delete($id);
    }

    public function computeVoterHash($pollId, $isAnonymous, $userId = null, $deviceToken = null) {
        if ($isAnonymous) {
            $token = $deviceToken ?? ($_COOKIE['sp_device_token'] ?? null);
            if (!$userId && !$token) {
                return null;
            }
            $identitySource = $userId ? $userId : $token;
            return hash('sha256', $pollId . '_' . $identitySource . '_' . Security::sanitize($_SERVER['HTTP_USER_AGENT'] ?? ''));
        }
        
        return $userId ? hash('sha256', $pollId . '_' . $userId) : null;
    }
}
