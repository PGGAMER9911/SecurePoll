<?php
namespace app\Models;

use app\Core\Database;
use PDO;
use PDOException;

class Poll {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO polls (creator_id, title, description, category, banner_image, rules, language, visibility, voting_type, is_anonymous, result_visibility, max_voters, min_required_votes, start_time, end_time, auto_close, status) 
                    VALUES (:creator_id, :title, :description, :category, :banner_image, :rules, :language, :visibility, :voting_type, :is_anonymous, :result_visibility, :max_voters, :min_required_votes, :start_time, :end_time, :auto_close, :status)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':creator_id' => $data['creator_id'],
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':category' => $data['category'] ?? null,
                ':banner_image' => $data['banner_image'] ?? null,
                ':rules' => $data['rules'] ?? null,
                ':language' => $data['language'] ?? 'en',
                ':visibility' => $data['visibility'] ?? 'Public',
                ':voting_type' => $data['voting_type'] ?? 'Single',
                ':is_anonymous' => $data['is_anonymous'] ?? 1,
                ':result_visibility' => $data['result_visibility'] ?? 'Live',
                ':max_voters' => $data['max_voters'] ?? null,
                ':min_required_votes' => $data['min_required_votes'] ?? 0,
                ':start_time' => $data['start_time'] ?? null,
                ':end_time' => $data['end_time'] ?? null,
                ':auto_close' => $data['auto_close'] ?? 0,
                ':status' => $data['status'] ?? 'Draft'
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Poll creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        $sql = "SELECT * FROM polls WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllActivePublic() {
        $sql = "SELECT p.*, u.name as creator_name, u.id as creator_id 
                FROM polls p 
                LEFT JOIN users u ON p.creator_id = u.id 
                WHERE p.status != 'Draft' AND p.visibility = 'Public' 
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByCreatorAndTitle($creatorId, $title) {
        $sql = "SELECT * FROM polls WHERE creator_id = :creator_id AND LOWER(TRIM(title)) = LOWER(TRIM(:title))";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':creator_id' => $creatorId, ':title' => $title]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalVotesForCreator($creator_id) {
        $sql = "SELECT COUNT(v.id) as total_votes 
                FROM votes v 
                JOIN polls p ON v.poll_id = p.id 
                WHERE p.creator_id = :creator_id AND p.status != 'Draft' AND p.visibility = 'Public'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':creator_id' => $creator_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_votes'] : 0;
    }

    public function getByCreator($creator_id) {
        $sql = "SELECT * FROM polls WHERE creator_id = :creator_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':creator_id' => $creator_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE polls SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        if (empty($fields)) {
            return true;
        }
        
        $sql = "UPDATE polls SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM polls WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

