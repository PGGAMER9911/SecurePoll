<?php
namespace app\Models;

use app\Core\Database;
use PDO;
use PDOException;

class Vote {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO votes (poll_id, option_id, user_id, voter_hash, ip_address) 
                    VALUES (:poll_id, :option_id, :user_id, :voter_hash, :ip_address)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':poll_id' => $data['poll_id'],
                ':option_id' => $data['option_id'],
                ':user_id' => $data['user_id'] ?? null,
                ':voter_hash' => $data['voter_hash'],
                ':ip_address' => $data['ip_address']
            ]);
        } catch (PDOException $e) {
            // Error code 23000 is integrity constraint violation (e.g. duplicate vote)
            if ($e->getCode() == 23000) {
                return 'DUPLICATE';
            }
            error_log("Vote casting failed: " . $e->getMessage());
            return false;
        }
    }

    public function getResultsByPoll($poll_id) {
        $sql = "SELECT option_id, COUNT(id) as vote_count FROM votes WHERE poll_id = :poll_id GROUP BY option_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $mapped = [];
        foreach($results as $row) {
            $mapped[$row['option_id']] = (int)$row['vote_count'];
        }
        return $mapped;
    }
    
    public function anyVotesExist($poll_id) {
        $sql = "SELECT id FROM votes WHERE poll_id = :poll_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
    
    public function anyVotesExistForUpdate($poll_id) {
        $sql = "SELECT id FROM votes WHERE poll_id = :poll_id LIMIT 1 FOR UPDATE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function hasVoted($poll_id, $voter_hash) {
        $sql = "SELECT id FROM votes WHERE poll_id = :poll_id AND voter_hash = :voter_hash LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id, ':voter_hash' => $voter_hash]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function hasVotedByUserId($poll_id, $user_id) {
        $sql = "SELECT id FROM votes WHERE poll_id = :poll_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id, ':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
    
    public function getTotalVotes($poll_id) {
        $sql = "SELECT COUNT(DISTINCT voter_hash) as total FROM votes WHERE poll_id = :poll_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id]);
        return (int)$stmt->fetchColumn();
    }
    
    public function getTotalVotesForCreator($creator_id) {
        $sql = "SELECT COUNT(DISTINCT v.voter_hash) as total FROM votes v 
                JOIN polls p ON v.poll_id = p.id 
                WHERE p.creator_id = :creator_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':creator_id' => $creator_id]);
        return (int)$stmt->fetchColumn();
    }

    public function getVoterActivity($poll_id, $limit, $offset) {
        $sql = "SELECT 
                    MIN(v.created_at) as vote_time,
                    u.name, 
                    u.id as voter_user_id,
                    v.voter_hash,
                    GROUP_CONCAT(po.option_text ORDER BY po.sort_order ASC SEPARATOR '||') as selected_options
                FROM votes v
                JOIN poll_options po ON v.option_id = po.id
                LEFT JOIN users u ON v.user_id = u.id
                WHERE v.poll_id = :poll_id
                GROUP BY v.voter_hash, u.id, u.name
                ORDER BY vote_time DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        // Bind parameters carefully for LIMIT/OFFSET
        $stmt->bindValue(':poll_id', $poll_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
