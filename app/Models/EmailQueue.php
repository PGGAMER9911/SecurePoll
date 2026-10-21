<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class EmailQueue extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function enqueue($toEmail, $subject, $bodyHtml, $bodyText = '') {
        $sql = "INSERT INTO email_queue (to_email, subject, body_html, body_text) VALUES (:to, :subject, :html, :text)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'to' => $toEmail,
            'subject' => $subject,
            'html' => $bodyHtml,
            'text' => $bodyText
        ]);
    }

    public function getPending($limit = 50) {
        // Use FOR UPDATE if supported/using innodb, but for simple cron it's fine without
        $stmt = $this->db->prepare("SELECT * FROM email_queue WHERE status = 'pending' OR (status = 'failed' AND attempts < 3) ORDER BY created_at ASC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markProcessing($id) {
        $stmt = $this->db->prepare("UPDATE email_queue SET status = 'processing', attempts = attempts + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function markSent($id) {
        $stmt = $this->db->prepare("UPDATE email_queue SET status = 'sent', sent_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function markFailed($id, $error) {
        $stmt = $this->db->prepare("UPDATE email_queue SET status = 'failed', error_message = :error WHERE id = :id");
        return $stmt->execute(['id' => $id, 'error' => $error]);
    }
}
