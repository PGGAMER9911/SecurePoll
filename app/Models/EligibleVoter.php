<?php
namespace app\Models;

use app\Core\Database;
use PDO;

class EligibleVoter {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($data) {
        try {
            $sql = "INSERT INTO eligible_voters (poll_id, email, name, department, year_level, is_registered) 
                    VALUES (:poll_id, :email, :name, :department, :year_level, :is_registered)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':poll_id' => $data['poll_id'],
                ':email' => $data['email'],
                ':name' => $data['name'] ?? null,
                ':department' => $data['department'] ?? null,
                ':year_level' => $data['year_level'] ?? null,
                ':is_registered' => $data['is_registered'] ?? 0
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function isEligible($poll_id, $email) {
        $sql = "SELECT id FROM eligible_voters WHERE poll_id = :poll_id AND email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $poll_id, ':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
