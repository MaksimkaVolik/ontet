<?php
namespace App\Models\Forum;

use Core\Database;

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getThreads($categoryId, $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;

        return $this->db->query("
            SELECT 
                t.*, 
                u.username, 
                u.avatar,
                COUNT(p.id) as posts_count,
                MAX(p.created_at) as last_post_date
            FROM threads t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN posts p ON p.thread_id = t.id
            WHERE t.category_id = :category_id
            GROUP BY t.id
            ORDER BY t.is_sticky DESC, last_post_date DESC
            LIMIT :offset, :per_page
        ", [
            'category_id' => $categoryId,
            'offset' => $offset,
            'per_page' => $perPage
        ])->fetchAll();
    }
}