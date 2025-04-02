<?php
namespace App\Models\Forum;

use Core\Database;

class Search {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function query($searchTerm, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        return $this->db->query(
            "SELECT t.*, 
                    MATCH(t.title, t.content) AGAINST(:search) as relevance,
                    u.username, u.avatar
             FROM threads t
             JOIN users u ON t.user_id = u.id
             WHERE MATCH(t.title, t.content) AGAINST(:search IN BOOLEAN MODE)
             ORDER BY relevance DESC
             LIMIT :offset, :limit",
            [
                'search' => $this->prepareSearchTerm($searchTerm),
                'offset' => $offset,
                'limit' => $perPage
            ]
        )->fetchAll();
    }

    private function prepareSearchTerm($term) {
        $words = preg_split('/\s+/', trim($term));
        return implode('* ', $words) . '*';
    }
}