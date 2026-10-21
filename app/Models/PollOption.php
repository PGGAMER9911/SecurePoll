<?php
namespace app\Models;

use app\Core\Database;
use PDO;
use PDOException;

class PollOption {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO poll_options (poll_id, option_text, option_image, sort_order) 
                    VALUES (:poll_id, :option_text, :option_image, :sort_order)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':poll_id' => $data['poll_id'],
                ':option_text' => $data['option_text'],
                ':option_image' => $data['option_image'] ?? null,
                ':sort_order' => $data['sort_order'] ?? 0
            ]);
        } catch (PDOException $e) {
            error_log("PollOption creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getByPollId($poll_id) {
        $sql = "SELECT * FROM poll_options WHERE poll_id = :poll_id ORDER BY sort_order ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOption($id, $text, $sortOrder) {
        $sql = "UPDATE poll_options SET option_text = :text, sort_order = :sort_order WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':text' => $text, ':sort_order' => $sortOrder, ':id' => $id]);
    }

    public function deleteOption($id) {
        $sql = "DELETE FROM poll_options WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function deleteByPollId($poll_id) {
        $sql = "DELETE FROM poll_options WHERE poll_id = :poll_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':poll_id' => $poll_id]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM poll_options WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
