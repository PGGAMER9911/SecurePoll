<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class NotificationReminder extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function getForPoll($pollId) {
        $stmt = $this->db->prepare("SELECT * FROM notification_reminders WHERE poll_id = :poll_id ORDER BY reminder_hours DESC");
        $stmt->execute(['poll_id' => $pollId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($pollId, $hours) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO notification_reminders (poll_id, reminder_hours) VALUES (:poll_id, :hours)");
        return $stmt->execute(['poll_id' => $pollId, 'hours' => $hours]);
    }

    public function clearForPoll($pollId) {
        $stmt = $this->db->prepare("DELETE FROM notification_reminders WHERE poll_id = :poll_id");
        return $stmt->execute(['poll_id' => $pollId]);
    }
}
