<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

class Poll {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function create(
        int $threadId,
        string $question,
        array $options,
        bool $isAnonymous = false,
        bool $isMultiple = false,
        ?string $endDate = null
    ): int {
        $this->db->beginTransaction();
        
        try {
            $this->db->query(
                "INSERT INTO polls 
                 (thread_id, question, is_anonymous, is_multiple, ends_at)
                 VALUES (:thread_id, :question, :anon, :multi, :ends_at)",
                [
                    'thread_id' => $threadId,
                    'question' => $question,
                    'anon' => $isAnonymous,
                    'multi' => $isMultiple,
                    'ends_at' => $endDate
                ]
            );
            
            $pollId = $this->db->lastInsertId();
            
            foreach ($options as $option) {
                $this->db->query(
                    "INSERT INTO poll_options (poll_id, text)
                     VALUES (:poll_id, :text)",
                    ['poll_id' => $pollId, 'text' => $option]
                );
            }
            
            $this->db->commit();
            return $pollId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
public function isActive(int $pollId): bool {
    $poll = $this->db->query(
        "SELECT ends_at FROM polls WHERE id = :id",
        ['id' => $pollId]
    )->fetch();
    return !$poll['ends_at'] || strtotime($poll['ends_at']) > time();
}