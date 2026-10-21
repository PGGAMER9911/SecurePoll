<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class Notification extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        // Prevent duplicate by group_key if specified
        if (!empty($data['group_key'])) {
            $existing = $this->findByGroupKey($data['user_id'], $data['group_key']);
            if ($existing) {
                return $this->updateGrouped($existing['id'], $data);
            }
        }

        $sql = "INSERT INTO notifications 
                (user_id, type, category, title, message, icon, entity_type, entity_id, action_type, group_key, group_count)
                VALUES 
                (:user_id, :type, :category, :title, :message, :icon, :entity_type, :entity_id, :action_type, :group_key, :group_count)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $data['user_id'],
            'type' => $data['type'] ?? '',
            'category' => $data['category'] ?? null,
            'title' => $data['title'],
            'message' => $data['message'],
            'icon' => $data['icon'] ?? null,
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'action_type' => $data['action_type'] ?? null,
            'group_key' => $data['group_key'] ?? null,
            'group_count' => $data['group_count'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function findByGroupKey($userId, $groupKey) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = :user_id AND group_key = :group_key");
        $stmt->execute(['user_id' => $userId, 'group_key' => $groupKey]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateGrouped($id, $data) {
        // Increment group_count and update message/title/is_read
        $sql = "UPDATE notifications SET 
                title = :title, 
                message = :message, 
                group_count = group_count + 1, 
                is_read = 0 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title' => $data['title'],
            'message' => $data['message'],
            'id' => $id
        ]);
    }

    public function getForUser($userId, $limit = 50, $offset = 0, $filter = 'all') {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id";
        
        if ($filter === 'unread') {
            $sql .= " AND is_read = 0";
        } elseif ($filter === 'read') {
            $sql .= " AND is_read = 1";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($id, $userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function clearAll($userId) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
}
