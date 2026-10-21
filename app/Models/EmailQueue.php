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
        $sql = "SELECT id, to_email, subject, body_html, body_text, status, attempts, created_at, processed_at, sent_at 
                FROM email_queue 
                WHERE status = 'pending' 
                   OR (status = 'failed' AND attempts < 3)
                   OR (status = 'processing' AND processed_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 10 MINUTE) AND attempts < 3)
                ORDER BY created_at ASC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Atomically claim a pending/failed/stale row for processing.
     * Prevents race conditions between concurrent workers.
     *
     * @param int $id
     * @return bool True if successfully claimed exclusively, false if already claimed
     */
    public function claimForProcessing($id) {
        $sql = "UPDATE email_queue 
                SET status = 'processing', 
                    attempts = attempts + 1, 
                    processed_at = CURRENT_TIMESTAMP 
                WHERE id = :id 
                  AND (
                      status = 'pending' 
                      OR (status = 'failed' AND attempts < 3)
                      OR (status = 'processing' AND processed_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 10 MINUTE) AND attempts < 3)
                  )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function markProcessing($id) {
        return $this->claimForProcessing($id);
    }

    public function markSent($id) {
        $stmt = $this->db->prepare("UPDATE email_queue SET status = 'sent', sent_at = CURRENT_TIMESTAMP, error_message = NULL WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function markFailed($id, $error) {
        $stmt = $this->db->prepare("UPDATE email_queue SET status = 'failed', error_message = :error WHERE id = :id");
        return $stmt->execute(['id' => $id, 'error' => substr($error, 0, 500)]);
    }

    public function getQueueSummary() {
        $stmt = $this->db->query("SELECT status, COUNT(*) as cnt FROM email_queue GROUP BY status");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function reclaimStaleProcessing($timeoutMinutes = 10) {
        $sql = "UPDATE email_queue 
                SET status = 'pending' 
                WHERE status = 'processing' 
                  AND processed_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL :mins MINUTE)
                  AND attempts < 3";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mins', (int)$timeoutMinutes, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
