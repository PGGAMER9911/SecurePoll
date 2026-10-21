<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Services\PollService;
use app\Models\Poll;
use app\Models\User;
use app\Models\PollInvitation;
use app\Helpers\Session;
use app\Helpers\Security;
use app\Middleware\RateLimiter;

class PollController extends Controller {
    private $pollService;
    private $pollModel;
    private $pollInvitationModel;
    private $userModel;

    public function __construct() {
        $this->pollService = new PollService();
        $this->pollModel = new Poll();
        $this->pollInvitationModel = new PollInvitation();
        $this->userModel = new User();
    }

    private function checkAccess($poll) {
        if ($poll['visibility'] === 'Public' || $poll['visibility'] === 'Unlisted') {
            return true;
        }
        
        if ($poll['visibility'] === 'Private') {
            if (!Session::get('user_id')) {
                return false;
            }
            if ($poll['creator_id'] == Session::get('user_id')) {
                return true;
            }
            $userEmail = Session::get('user_email');
            if (!$userEmail) {
                $userRec = $this->userModel->findById(Session::get('user_id'));
                $userEmail = $userRec['email'] ?? null;
                if ($userEmail) {
                    Session::set('user_email', $userEmail);
                }
            }
            if ($userEmail) {
                $invitation = $this->pollInvitationModel->getByPollAndEmail($poll['id'], $userEmail);
                if ($invitation && $invitation['status'] === 'Accepted') {
                    return true;
                }
            }
            $token = $_GET['token'] ?? $_POST['invitation_token'] ?? null;
            if ($token) {
                $inv = $this->pollInvitationModel->getByToken($token);
                if ($inv && $inv['poll_id'] == $poll['id'] && in_array($inv['status'], ['Pending', 'Accepted'], true)) {
                    return true;
                }
            }
            return false;
        }
        
        return false;
    }

    public function index() {
        $polls = $this->pollModel->getAllActivePublic();
        return $this->view('polls/index', ['polls' => $polls]);
    }

    public function myPolls() {
        if (!Session::get('user_id')) {
            Session::setFlash('error', 'You must be logged in to view your polls.');
            return $this->redirect('/login');
        }
        $userId = Session::get('user_id');
        $polls = $this->pollModel->getByCreator($userId);
        return $this->view('polls/my_polls', ['polls' => $polls]);
    }

    public function create() {
        if (!Session::get('user_id')) {
            Session::setFlash('error', 'You must be logged in to create a poll.');
            return $this->redirect('/login');
        }
        return $this->view('polls/create');
    }

    public function store() {
        if (!Session::get('user_id')) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }

        $votingType = $_POST['voting_type'] ?? 'Single';

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'visibility' => $_POST['visibility'] ?? 'Public',
            'voting_type' => $votingType,
            'is_anonymous' => isset($_POST['is_anonymous']) && filter_var($_POST['is_anonymous'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'status' => 'Active'
        ];

        if (isset($_POST['max_voters']) && trim($_POST['max_voters']) !== '') {
            $maxVoters = (int)$_POST['max_voters'];
            if ($maxVoters < 2) {
                return $this->json(['success' => false, 'message' => 'Max voters must be at least 2.']);
            }
            $data['max_voters'] = $maxVoters;
        }
        
        $now = new \DateTime();
        $nowMinute = new \DateTime($now->format('Y-m-d H:i:00'));

        if (!empty($_POST['start_time'])) {
            try {
                $startTime = new \DateTime(trim($_POST['start_time']));
                $startMinute = new \DateTime($startTime->format('Y-m-d H:i:00'));
                
                if ($startMinute < $nowMinute) {
                    return $this->json(['success' => false, 'message' => 'Start time cannot be in the past.']);
                }
                $data['start_time'] = $startTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => 'Invalid start time format.']);
            }
        }
        
        if (!empty($_POST['end_time'])) {
            try {
                $endTime = new \DateTime(trim($_POST['end_time']));
                $endMinute = new \DateTime($endTime->format('Y-m-d H:i:00'));
                
                if ($endMinute < $nowMinute) {
                    return $this->json(['success' => false, 'message' => 'End time cannot be in the past.']);
                }
                
                if (!empty($data['start_time'])) {
                    $startMinute = new \DateTime((new \DateTime($data['start_time']))->format('Y-m-d H:i:00'));
                    if ($endMinute <= $startMinute) {
                        return $this->json(['success' => false, 'message' => 'End time must be after the start time.']);
                    }
                }
                
                $data['end_time'] = $endTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => 'Invalid end time format.']);
            }
        }
        
        if ($votingType === 'Yes/No') {
            $options = ['Yes', 'No'];
            if (isset($_POST['allow_third_option']) && !empty($_POST['third_option_label'])) {
                $options[] = $_POST['third_option_label'];
            }
        } elseif ($votingType === 'Rating') {
            $scale = isset($_POST['rating_scale']) ? (int)$_POST['rating_scale'] : 5;
            $options = [];
            for ($i = 0.5; $i <= $scale; $i += 0.5) {
                $options[] = number_format($i, 1);
            }
        } else {
            $options = $_POST['options'] ?? [];
        }

        $eligibleVoters = $_POST['eligible_voters'] ?? '';
        
        if (empty($data['title']) || count($options) < 2) {
            return $this->json(['success' => false, 'message' => 'Title and at least 2 options are required.']);
        }

        $result = $this->pollService->createPoll(Session::get('user_id'), $data, $options, $eligibleVoters);
        
        return $this->json($result);
    }

    public function update() {
        if (!Session::get('user_id')) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $id = $_POST['poll_id'] ?? null;
        if (!$id) return $this->json(['success' => false, 'message' => 'No poll ID specified.']);
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $poll = $this->pollModel->getById($id);
        if (!$poll || $poll['creator_id'] != Session::get('user_id')) {
            return $this->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $voteModel = new \app\Models\Vote();
        $distinctVoters = $voteModel->getTotalVotes($id);

        $votingType = $_POST['voting_type'] ?? $poll['voting_type'];

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'visibility' => $_POST['visibility'] ?? $poll['visibility'],
            'voting_type' => $votingType,
            'is_anonymous' => isset($_POST['is_anonymous']) && filter_var($_POST['is_anonymous'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0
        ];

        if (isset($_POST['max_voters']) && trim($_POST['max_voters']) !== '') {
            $maxVoters = (int)$_POST['max_voters'];
            if ($maxVoters < 1) {
                return $this->json(['success' => false, 'message' => 'Max voters must be at least 1.']);
            }
            if ($distinctVoters > 0 && $maxVoters < $distinctVoters) {
                return $this->json(['success' => false, 'message' => 'Max voters cannot be less than current voter count (' . $distinctVoters . ').']);
            }
            $data['max_voters'] = $maxVoters;
        } else {
            $data['max_voters'] = '';
        }
        
        $now = new \DateTime();
        $nowMinute = new \DateTime($now->format('Y-m-d H:i:00'));

        $startTimeStr = trim($_POST['start_time'] ?? '');
        if ($startTimeStr !== '') {
            try {
                $startTime = new \DateTime($startTimeStr);
                $startMinute = new \DateTime($startTime->format('Y-m-d H:i:00'));
                
                $existingStartTime = $poll['start_time'] ? new \DateTime($poll['start_time']) : null;
                $existingStartMinute = $existingStartTime ? new \DateTime($existingStartTime->format('Y-m-d H:i:00')) : null;
                
                if (!$existingStartMinute || $startMinute != $existingStartMinute) {
                    if ($startMinute < $nowMinute) {
                        return $this->json(['success' => false, 'message' => 'Start time cannot be in the past.']);
                    }
                }
                $data['start_time'] = $startTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => 'Invalid start time format.']);
            }
        } else {
            $data['start_time'] = '';
        }
        
        $endTimeStr = trim($_POST['end_time'] ?? '');
        if ($endTimeStr !== '') {
            try {
                $endTime = new \DateTime($endTimeStr);
                $endMinute = new \DateTime($endTime->format('Y-m-d H:i:00'));
                
                $existingEndTime = $poll['end_time'] ? new \DateTime($poll['end_time']) : null;
                $existingEndMinute = $existingEndTime ? new \DateTime($existingEndTime->format('Y-m-d H:i:00')) : null;
                
                if (!$existingEndMinute || $endMinute != $existingEndMinute) {
                    if ($endMinute < $nowMinute) {
                        return $this->json(['success' => false, 'message' => 'End time cannot be in the past.']);
                    }
                }
                
                if (!empty($data['start_time'])) {
                    $startMinute = new \DateTime((new \DateTime($data['start_time']))->format('Y-m-d H:i:00'));
                    if ($endMinute <= $startMinute) {
                        return $this->json(['success' => false, 'message' => 'End time must be after the start time.']);
                    }
                }
                
                $data['end_time'] = $endTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => 'Invalid end time format.']);
            }
        } else {
            $data['end_time'] = '';
        }
        
        if ($votingType === 'Yes/No') {
            $options = ['Yes', 'No'];
            if (isset($_POST['allow_third_option']) && !empty($_POST['third_option_label'])) {
                $options[] = $_POST['third_option_label'];
            }
        } elseif ($votingType === 'Rating') {
            $scale = isset($_POST['rating_scale']) ? (int)$_POST['rating_scale'] : 5;
            $options = [];
            for ($i = 0.5; $i <= $scale; $i += 0.5) {
                $options[] = number_format($i, 1);
            }
        } else {
            $options = $_POST['options'] ?? [];
        }

        if (empty($data['title'])) {
            return $this->json(['success' => false, 'message' => 'Title is required.']);
        }
        
        if (!$voteModel->anyVotesExist($id) && count($options) < 2) {
            return $this->json(['success' => false, 'message' => 'At least 2 options are required.']);
        }

        $result = $this->pollService->updatePoll($id, Session::get('user_id'), $data, $options);
        
        if (!empty($result['success'])) {
            $invitations = $this->pollInvitationModel->getByPollId($id);
            if (!empty($invitations)) {
                $notificationService = new \app\Services\NotificationService();
                $creatorId = Session::get('user_id');
                foreach ($invitations as $inv) {
                    if (!empty($inv['email'])) {
                        $invitee = $this->userModel->findByEmail($inv['email']);
                        if ($invitee && $invitee['id'] != $creatorId) {
                            $notificationService->notifyPollUpdated($invitee['id'], $id, $data['title']);
                        }
                    }
                }
            }
        }
        
        return $this->json($result);
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) return $this->view('errors/404');
        
        $poll = $this->pollService->getPollWithDetails($id);
        if (!$poll) {
            return $this->view('errors/404');
        }
        
        if (!$this->checkAccess($poll)) {
            Session::setFlash('error', "You don't have access to this private poll.");
            return $this->redirect('/polls');
        }
        
        $hasVoted = false;
        
        $userId = Session::get('user_id');
        $hash = $this->pollService->computeVoterHash($id, (bool)$poll['is_anonymous'], $userId);
        
        if ($hash) {
            $voteModel = new \app\Models\Vote();
            $hasVoted = $voteModel->hasVoted($id, $hash);
            if (!$hasVoted && $userId) {
                $hasVoted = $voteModel->hasVotedByUserId($id, $userId);
            }
        }

        return $this->view('polls/show', [
            'poll' => $poll,
            'hasVoted' => $hasVoted
        ]);
    }

    public function vote() {
        RateLimiter::enforce('vote', 30, 60);
        $this->requireAuthApi();
        $userId = Session::get('user_id');

        $id = $_POST['poll_id'] ?? null;
        if (!$id) return $this->json(['success' => false, 'message' => 'No poll ID specified.'], 400);
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $poll = $this->pollModel->getById($id);
        if (!$poll) {
             return $this->json(['success' => false, 'message' => 'Poll not found.'], 404);
        }
        if (!$this->checkAccess($poll)) {
             return $this->json(['success' => false, 'message' => "You don't have access to this private poll."], 403);
        }
        
        if ($poll['creator_id'] == $userId) {
             return $this->json(['success' => false, 'message' => 'Creators cannot vote on their own poll.'], 403);
        }
        
        $optionId = $_POST['option_id'] ?? null;
        $options = $_POST['options'] ?? null;
        
        if (!$optionId && empty($options)) {
             return $this->json(['success' => false, 'message' => 'No option selected.'], 400);
        }
        
        $voteData = !empty($options) ? $options : $optionId;
        
        $userEmail = Session::get('user_email');
        if (!$userEmail && $userId) {
            $userRec = $this->userModel->findById($userId);
            $userEmail = $userRec['email'] ?? null;
            if ($userEmail) {
                Session::set('user_email', $userEmail);
            }
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        $result = $this->pollService->castVote($id, $voteData, $userId, $userEmail, $ip);
        
        $status = ($result['success'] ?? false) ? 200 : 400;
        return $this->json($result, $status);
    }
    
    public function results() {
        $id = $_GET['id'] ?? null;
        if (!$id) return $this->json(['success' => false, 'message' => 'No poll ID specified.'], 400);
        
        $poll = $this->pollService->getPollWithDetails($id);
        if (!$poll) {
             return $this->json(['success' => false, 'message' => 'Poll not found.'], 404);
        }
        
        if (!$this->checkAccess($poll)) {
             return $this->json(['success' => false, 'message' => "You don't have access to this private poll."], 403);
        }
        
        if ($poll['result_visibility'] === 'Hidden' && Session::get('user_id') != $poll['creator_id']) {
            return $this->json(['success' => false, 'message' => 'Results are hidden for this poll.'], 403);
        }
        
        $results = $this->pollService->getResults($id);
        
        $labels = [];
        $data = [];
        
        foreach ($poll['options'] as $option) {
            $labels[] = $option['option_text'];
            $data[] = $results[$option['id']] ?? 0;
        }
        
        return $this->json([
            'success' => true,
            'labels' => $labels,
            'data' => $data
        ]);
    }
    
    public function manage() {
        $this->requireAuth();
        $id = $_GET['id'] ?? null;
        if (!$id) return $this->view('errors/404');
        
        $poll = $this->pollService->getPollWithDetails($id);
        if (!$poll || $poll['creator_id'] != Session::get('user_id')) {
            Session::setFlash('error', 'Unauthorized access.');
            return $this->redirect('/polls');
        }
        
        return $this->view('polls/manage', ['poll' => $poll]);
    }

    public function importCsv() {
        $this->requireAuthApi();
        $id = $_POST['poll_id'] ?? null;
        if (!$id || empty($_FILES['csv_file'])) {
             return $this->json(['success' => false, 'message' => 'Missing parameters.']);
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $uploadError = $_FILES['csv_file']['error'];
        if ($uploadError !== UPLOAD_ERR_OK) {
            $messages = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];
            $msg = $messages[$uploadError] ?? 'Unknown upload error.';
            return $this->json(['success' => false, 'message' => $msg]);
        }

        $maxFileSize = \app\Helpers\Env::get('CSV_MAX_FILE_SIZE', 5242880);
        if ($_FILES['csv_file']['size'] > $maxFileSize) {
            return $this->json(['success' => false, 'message' => 'File size exceeds the application limit.']);
        }

        $file = $_FILES['csv_file']['tmp_name'];
        if (!is_readable($file)) {
            return $this->json(['success' => false, 'message' => 'Uploaded file is not readable.']);
        }

        $result = $this->pollService->importCsv($id, $file, Session::get('user_id'));
        
        return $this->json($result);
    }

    public function delete() {
        $this->requireAuth();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            Session::setFlash('error', 'No poll ID specified.');
            return $this->redirect('/polls/my-polls');
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $userId = Session::get('user_id');
        if ($this->pollService->deletePoll($id, $userId)) {
            Session::setFlash('success', 'Poll deleted successfully.');
        } else {
            Session::setFlash('error', 'Failed to delete poll or unauthorized access.');
        }
        
        return $this->redirect('/polls/my-polls');
    }

    public function searchUsers() {
        $id = $_GET['poll_id'] ?? null;
        $query = trim($_GET['q'] ?? '');

        $this->requireAuthApi();
        
        if (!$id || strlen($query) < 2) {
             return $this->json(['success' => true, 'data' => []]);
        }
        
        $poll = $this->pollModel->getById($id);
        if (!$poll || $poll['creator_id'] != Session::get('user_id')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $users = $this->userModel->searchVerifiedUsers($query);
        $results = [];
        
        $voteModel = new \app\Models\Vote();

        foreach ($users as $user) {
            $isCreator = ($user['id'] == Session::get('user_id'));
            
            $invitation = $this->pollInvitationModel->getByPollAndEmail($id, $user['email']);
            $status = $invitation ? $invitation['status'] : null;

            $hasVoted = false;
            if (!$poll['is_anonymous']) {
                $hasVoted = $voteModel->hasVotedByUserId($id, $user['id']);
            }

            $parts = explode('@', $user['email']);
            $maskedEmail = substr($parts[0], 0, 1) . '***@' . $parts[1];
            
            $results[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'masked_email' => $maskedEmail,
                'full_email' => $user['email'],
                'is_creator' => $isCreator,
                'invitation_status' => $status,
                'has_voted' => $hasVoted
            ];
        }
        
        return $this->json(['success' => true, 'data' => $results]);
    }
    
    public function inviteUser() {
        $this->requireAuthApi();
        $pollId = $_POST['poll_id'] ?? null;
        $email = trim($_POST['email'] ?? '');
        
        if (!$pollId || !$email) {
             return $this->json(['success' => false, 'message' => 'Missing parameters.']);
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $poll = $this->pollModel->getById($pollId);
        if (!$poll || $poll['creator_id'] != Session::get('user_id')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $creator = $this->userModel->findById(Session::get('user_id'));
        $creatorName = $creator['name'] ?? 'The poll creator';
        $notificationService = new \app\Services\NotificationService();
        $user = $this->userModel->findByEmail($email);
        
        $existing = $this->pollInvitationModel->getByPollAndEmail($pollId, $email);
        
        if ($existing) {
            if ($existing['status'] === 'Pending') {
                return $this->json(['success' => false, 'message' => 'Invitation already sent.']);
            }
            if ($existing['status'] === 'Accepted') {
                return $this->json(['success' => false, 'message' => 'User already has access to this poll.']);
            }
            if ($existing['status'] === 'Declined' || $existing['status'] === 'Revoked') {
                $this->pollInvitationModel->updateStatus($existing['id'], 'Pending');
                if ($user) {
                    $notificationService->notifyNewInvitation(
                        $user['id'], 
                        $pollId, 
                        $poll['title'], 
                        $creatorName, 
                        $existing['invitation_token'], 
                        $poll['end_time'] ?? null
                    );
                }
                return $this->json(['success' => true, 'message' => 'Invitation resent.']);
            }
        }
        
        $token = bin2hex(random_bytes(16));
        $this->pollInvitationModel->create([
            'poll_id' => $pollId,
            'email' => $email,
            'invitation_token' => $token,
            'status' => 'Pending'
        ]);
        
        if ($user) {
            $notificationService->notifyNewInvitation(
                $user['id'], 
                $pollId, 
                $poll['title'], 
                $creatorName, 
                $token, 
                $poll['end_time'] ?? null
            );
        } else {
            // Unregistered recipient: Queue email invitation with token link
            $emailQueue = new \app\Models\EmailQueue();
            $baseUrl = \app\Helpers\Env::get('APP_URL', 'http://localhost/SecurePoll/public');
            $inviteUrl = "{$baseUrl}/polls/show?id={$pollId}&token={$token}";
            $emailHtml = "
            <!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1.0'></head>
            <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px; margin: 0;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);'>
                    <div style='font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 24px; text-align: center;'>Secure<span style='color: #6366f1;'>Poll</span></div>
                    <div style='font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 16px; text-align: center;'>Poll Invitation: " . htmlspecialchars($poll['title']) . "</div>
                    <div style='font-size: 15px; color: #475569; line-height: 1.6; text-align: center; margin-bottom: 24px;'>" . htmlspecialchars($creatorName) . " has invited you to vote in '<strong>" . htmlspecialchars($poll['title']) . "</strong>'.</div>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$inviteUrl}' style='display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; font-size: 14px;'>View & Vote in Poll</a>
                    </div>
                </div>
            </body></html>";
            $emailQueue->enqueue($email, 'Poll Invitation: ' . $poll['title'], $emailHtml, "type:new_invitation");
        }
        
        return $this->json(['success' => true, 'message' => 'Invitation sent.']);
    }
    
    public function getInvitations() {
        $this->requireAuthApi();
        $pollId = $_GET['poll_id'] ?? null;
        if (!$pollId) return $this->json(['success' => false, 'message' => 'Missing poll ID.']);
        
        $poll = $this->pollModel->getById($pollId);
        if (!$poll || $poll['creator_id'] != Session::get('user_id')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $invitations = $this->pollInvitationModel->getByPollId($pollId);
        $voteModel = new \app\Models\Vote();
        
        foreach ($invitations as &$inv) {
            $inv['has_voted'] = false;
            if (!$poll['is_anonymous'] && !empty($inv['user_name'])) {
                // To check hasVoted we need user ID. getByPollId returns user_name and email, but we need user ID.
                $user = $this->userModel->findByEmail($inv['email']);
                if ($user) {
                    $inv['has_voted'] = $voteModel->hasVotedByUserId($pollId, $user['id']);
                }
            }
        }
        
        return $this->json(['success' => true, 'data' => $invitations, 'is_anonymous' => (bool)$poll['is_anonymous']]);
    }
    
    public function revokeInvitation() {
        $this->requireAuthApi();
        $invitationId = $_POST['invitation_id'] ?? null;
        
        if (!$invitationId) {
             return $this->json(['success' => false, 'message' => 'Missing invitation ID.']);
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $invitation = $this->pollInvitationModel->getById($invitationId);
        if (!$invitation) {
            return $this->json(['success' => false, 'message' => 'Invitation not found.']);
        }
        
        $poll = $this->pollModel->getById($invitation['poll_id']);
        if (!$poll || $poll['creator_id'] != Session::get('user_id')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $this->pollInvitationModel->updateStatus($invitationId, 'Revoked');
        
        return $this->json(['success' => true, 'message' => 'Invitation revoked.']);
    }

    public function voterActivity() {
        $this->requireAuthApi();
        $pollId = $_GET['poll_id'] ?? null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        if (!$pollId) {
             return $this->json(['success' => false, 'message' => 'Missing poll ID.'], 400);
        }
        
        $poll = $this->pollModel->getById($pollId);
        if (!$poll) {
             return $this->json(['success' => false, 'message' => 'Poll not found.'], 404);
        }
        
        if ($poll['creator_id'] != Session::get('user_id')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        
        $result = $this->pollService->getVoterActivity($pollId, $page, 50);
        
        return $this->json($result);
    }
}
