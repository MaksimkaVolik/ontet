<?php
namespace App\Models;

use Core\Database;

class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($userId, $typeName, $relatedId = null, $relatedType = null) {
        $type = $this->db->query(
            "SELECT id FROM notification_types WHERE name = :name LIMIT 1",
            ['name' => $typeName]
        )->fetch();

        if (!$type) return false;

        $this->db->query(
            "INSERT INTO notifications (user_id, type_id, related_id, related_type)
             VALUES (:user_id, :type_id, :related_id, :related_type)",
            [
                'user_id' => $userId,
                'type_id' => $type['id'],
                'related_id' => $relatedId,
                'related_type' => $relatedType
            ]
        );
        return $this->db->lastInsertId();
    }

    public function markAsRead($notificationId, $userId) {
        $this->db->query(
            "UPDATE notifications SET is_read = TRUE
             WHERE id = :id AND user_id = :user_id",
            ['id' => $notificationId, 'user_id' => $userId]
        );
        return $this->db->rowCount() > 0;
    }

    public function getForUser($userId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        return $this->db->query(
            "SELECT n.*, nt.name as type_name, nt.template
             FROM notifications n
             JOIN notification_types nt ON n.type_id = nt.id
             WHERE n.user_id = :user_id
             ORDER BY n.created_at DESC
             LIMIT :offset, :limit",
            [
                'user_id' => $userId,
                'offset' => $offset,
                'limit' => $perPage
            ]
        )->fetchAll();
    }

    public function getUnreadCount($userId) {
        return $this->db->query(
            "SELECT COUNT(*) as count FROM notifications
             WHERE user_id = :user_id AND is_read = FALSE",
            ['user_id' => $userId]
        )->fetch()['count'];
    }
}