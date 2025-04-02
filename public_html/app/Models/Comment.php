<?php
namespace App\Models;

use Core\Database;

class Comment {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getThreaded(int $threadId): array {
        $comments = $this->db->query(
            "SELECT p.*, u.username, u.avatar 
             FROM posts p
             JOIN users u ON p.user_id = u.id
             WHERE p.thread_id = :thread_id
             ORDER BY p.created_at ASC",
            ['thread_id' => $threadId]
        )->fetchAll();

        return $this->buildTree($comments);
    }

    private function buildTree(array $comments, ?int $parentId = null): array {
        $branch = [];

        foreach ($comments as $comment) {
            if ($comment['parent_id'] === $parentId) {
                $children = $this->buildTree($comments, $comment['id']);
                if ($children) {
                    $comment['children'] = $children;
                }
                $branch[] = $comment;
            }
        }

        return $branch;
    }

    public function addMentions(int $postId, array $usernames): void {
        foreach ($usernames as $username) {
            $userId = $this->db->query(
                "SELECT id FROM users WHERE username = :username LIMIT 1",
                ['username' => substr($username, 1)] // Убираем @
            )->fetchColumn();

            if ($userId) {
                $this->db->query(
                    "INSERT IGNORE INTO post_mentions (post_id, user_id)
                     VALUES (:post_id, :user_id)",
                    ['post_id' => $postId, 'user_id' => $userId]
                );
            }
        }
    }
}