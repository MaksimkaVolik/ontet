<?php
namespace App\Models\Forum;

use Core\Database;

class Thread {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($data) {
        $slug = $this->generateSlug($data['title']);
        $this->db->query("
            INSERT INTO threads (title, slug, content, user_id, category_id)
            VALUES (:title, :slug, :content, :user_id, :category_id)
        ", [
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id']
        ]);
        
        return $this->db->lastInsertId();
    }

    public function findBySlug($slug) {
        $thread = $this->db->query("
            SELECT t.*, u.username, u.avatar, u.status,
                   c.title as category_title, c.slug as category_slug
            FROM threads t
            JOIN users u ON t.user_id = u.id
            JOIN categories c ON t.category_id = c.id
            WHERE t.slug = :slug LIMIT 1
        ", ['slug' => $slug])->fetch();
        
        if ($thread) {
            $this->db->query("
                UPDATE threads SET views_count = views_count + 1 WHERE id = :id
            ", ['id' => $thread['id']]);
        }
        
        return $thread;
    }

    private function generateSlug($title) {
        $slug = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; Lower()', $title
        );
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $existing = $this->db->query("
            SELECT COUNT(*) as count FROM threads WHERE slug LIKE :slug
        ", ['slug' => $slug . '%'])->fetch()['count'];
        
        return $existing ? $slug . '-' . ($existing + 1) : $slug;
    }
}