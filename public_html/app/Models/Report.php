<?php
namespace App\Models;

use Core\Database;

class Report {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($userId, $contentType, $contentId, $reason) {
        return $this->db->query(
            "INSERT INTO reports 
             (user_id, content_type, content_id, reason) 
             VALUES (:user_id, :content_type, :content_id, :reason)",
            [
                'user_id' => $userId,
                'content_type' => $contentType,
                'content_id' => $contentId,
                'reason' => $reason
            ]
        );
    }

    public function getPendingReports($limit = 20) {
        return $this->db->query(
            "SELECT r.*, u.username as reporter_name
             FROM reports r
             JOIN users u ON r.user_id = u.id
             WHERE r.status = 'pending'
             ORDER BY r.created_at DESC
             LIMIT :limit",
            ['limit' => $limit]
        )->fetchAll();
    }

    public function resolveReport($reportId, $moderatorId, $resolution, $status) {
        return $this->db->query(
            "UPDATE reports SET 
              status = :status,
              moderator_id = :moderator_id,
              resolution_text = :resolution,
              resolved_at = NOW()
             WHERE id = :id",
            [
                'id' => $reportId,
                'status' => $status,
                'moderator_id' => $moderatorId,
                'resolution' => $resolution
            ]
        );
    }
}