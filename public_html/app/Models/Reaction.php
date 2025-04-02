<?php
namespace App\Models;

use Core\Database;
use Core\Cache\RedisCache;

class Reaction {
    private Database $db;
    private RedisCache $cache;

    public function __construct() {
        $this->db = new Database();
        $this->cache = new RedisCache();
    }

    public function add(int $postId, int $userId, string $type): array {
        $typeId = $this->getTypeId($type);
        
        $this->db->query(
            "INSERT INTO post_reactions (post_id, user_id, type_id)
             VALUES (:post_id, :user_id, :type_id)
             ON DUPLICATE KEY UPDATE type_id = VALUES(type_id)",
            [
                'post_id' => $postId,
                'user_id' => $userId,
                'type_id' => $typeId
            ]
        );

        $this->cache->invalidate("post_{$postId}_reactions");
        
        return $this->getPostReactions($postId);
    }

    public function getPostReactions(int $postId): array {
        $cacheKey = "post_{$postId}_reactions";
        
        return $this->cache->get($cacheKey, function() use ($postId) {
            $reactions = $this->db->query(
                "SELECT rt.name, rt.icon, rt.color, COUNT(pr.id) as count
                 FROM reaction_types rt
                 LEFT JOIN post_reactions pr ON rt.id = pr.type_id AND pr.post_id = :post_id
                 GROUP BY rt.id",
                ['post_id' => $postId]
            )->fetchAll();

            $userReaction = $this->db->query(
                "SELECT rt.name FROM post_reactions pr
                 JOIN reaction_types rt ON pr.type_id = rt.id
                 WHERE pr.post_id = :post_id AND pr.user_id = :user_id",
                [
                    'post_id' => $postId,
                    'user_id' => $_SESSION['user_id'] ?? 0
                ]
            )->fetchColumn();

            return [
                'reactions' => $reactions,
                'user_reaction' => $userReaction
            ];
        }, 300);
    }

    private function getTypeId(string $type): int {
        return $this->db->query(
            "SELECT id FROM reaction_types WHERE name = :name LIMIT 1",
            ['name' => $type]
        )->fetchColumn() ?? throw new \Exception("Invalid reaction type");
    }
}