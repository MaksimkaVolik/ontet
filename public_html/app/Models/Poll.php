<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

class Poll {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function isActive(int $pollId): bool {
        $poll = $this->db->query(
            "SELECT ends_at FROM polls WHERE id = :id",
            ['id' => $pollId]
        )->fetch();
        return !$poll['ends_at'] || strtotime($poll['ends_at']) > time();
    }

    public function addVote(int $pollId, int $optionId, int $userId): bool {
        if (!$this->isActive($pollId)) {
            return false;
        }

        return $this->db->query(
            "INSERT INTO poll_votes (poll_id, option_id, user_id) 
             VALUES (:poll_id, :option_id, :user_id)
             ON DUPLICATE KEY UPDATE option_id = VALUES(option_id)",
            [
                'poll_id' => $pollId,
                'option_id' => $optionId,
                'user_id' => $userId
            ]
        )->rowCount() > 0;
    }
}