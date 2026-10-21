<?php
namespace app\Models;

use app\Core\Database;
use PDO;
use PDOException;

class PollInvitation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO poll_invitations (poll_id, email, invitation_token, status) 
                    VALUES (:poll_id, :email, :invitation_token, :status)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':poll_id' => $data['poll_id'],
                ':email' => $data['email'],
                ':invitation_token' => $data['invitation_token'],
                ':status' => $data['status'] ?? 'Pending'
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Poll Invitation creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getByToken($token) {
        $sql = "SELECT * FROM poll_invitations WHERE invitation_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE poll_invitations SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function markAsSent($id) {
        $sql = "UPDATE poll_invitations SET status = 'Sent', sent_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function markAsUsed($id) {
        $sql = "UPDATE poll_invitations SET status = 'Accepted' WHERE id = :id AND status <> 'Accepted'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() === 1;
    }

    public function getById($id) {
        $sql = "SELECT * FROM poll_invitations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserEmail($email) {
        $sql = "SELECT pi.*, p.title as poll_title, u.name as creator_name 
                FROM poll_invitations pi 
                JOIN polls p ON pi.poll_id = p.id 
                JOIN users u ON p.creator_id = u.id 
                WHERE pi.email = :email 
                ORDER BY pi.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPollAndEmail($pollId, $email) {
        $sql = "SELECT * FROM poll_invitations WHERE poll_id = :poll_id AND email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $pollId, ':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByPollId($pollId) {
        $sql = "SELECT pi.*, u.name as user_name 
                FROM poll_invitations pi 
                LEFT JOIN users u ON pi.email = u.email 
                WHERE pi.poll_id = :poll_id 
                ORDER BY pi.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':poll_id' => $pollId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
