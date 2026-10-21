<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class User extends Model {
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        return $this->findById($id);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, name, email, password_hash) 
            VALUES (:role_id, :name, :email, :password_hash)
        ");
        $stmt->execute([
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash']
        ]);
        return $this->db->lastInsertId();
    }

    public function markAsVerified($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function updatePassword($id, $newPasswordHash) {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        return $stmt->execute(['hash' => $newPasswordHash, 'id' => $id]);
    }

    public function updateUnverifiedUser($id, $name, $passwordHash) {
        $stmt = $this->db->prepare("UPDATE users SET name = :name, password_hash = :hash WHERE id = :id AND is_verified = 0");
        return $stmt->execute(['name' => $name, 'hash' => $passwordHash, 'id' => $id]);
    }
    public function searchVerifiedUsers($query, $limit = 5) {
        $sql = "SELECT id, name, email FROM users 
                WHERE is_verified = 1 
                AND (name LIKE :query1 OR email LIKE :query2) 
                ORDER BY name ASC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        // Using bindValue because LIMIT requires integer binding in PDO sometimes
        $stmt->bindValue(':query1', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->bindValue(':query2', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $data) {
        $allowed = ['name', 'bio', 'college', 'department', 'course', 'semester', 'enrollment_id'];
        $fields = [];
        $params = ['id' => $id];
        foreach ($data as $key => $val) {
            if (in_array($key, $allowed, true)) {
                $fields[] = "$key = :$key";
                $params[$key] = $val;
            }
        }
        if (empty($fields)) {
            return false;
        }
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateTheme($id, $theme) {
        $stmt = $this->db->prepare("UPDATE users SET theme = :theme WHERE id = :id");
        return $stmt->execute(['theme' => $theme, 'id' => $id]);
    }
}

