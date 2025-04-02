<?php
declare(strict_types=1);

namespace App\Services\Feed;

use Core\Database;
use Core\Cache\RedisCache;

class FeedAlgorithm {
    private Database $db;
    private RedisCache $cache;

    public function __construct(Database $db, RedisCache $cache) {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function getPersonalizedFeed(int $userId, int $limit = 25): array {
        $cacheKey = "user_{$userId}_feed";
        
        return $this->cache->get($cacheKey, function() use ($userId, $limit) {
            $prefs = $this->getUserPreferences($userId);
            
            $query = match($prefs['feed_algorithm']) {
                'new' => $this->getNewContentQuery(),
                'top' => $this->getTopContentQuery(),
                'personalized' => $this->getPersonalizedQuery($userId),
                default => $this->getHotContentQuery()
            };

            return $this->db->query(
                $query . " LIMIT :limit",
                ['limit' => $limit]
            )->fetchAll();
        }, 300);
    }

    private function getUserPreferences(int $userId): array {
        return $this->db->query(
            "SELECT * FROM user_preferences WHERE user_id = :user_id",
            ['user_id' => $userId]
        )->fetch() ?? [
            'feed_algorithm' => 'hot',
            'preferred_categories' => '[]',
            'ignored_tags' => '[]'
        ];
    }

    private function getHotContentQuery(): string {
        return "SELECT t.*, 
                (t.views_count * 0.2 + COUNT(p.id) * 0.5 + 
                COUNT(pr.id) * 0.3) / POW(TIMESTAMPDIFF(HOUR, t.created_at, NOW()) + 2, 1.8) as score
                FROM threads t
                LEFT JOIN posts p ON p.thread_id = t.id
                LEFT JOIN post_reactions pr ON pr.post_id = p.id
                GROUP BY t.id
                ORDER BY score DESC";
    }

    private function getPersonalizedQuery(int $userId): string {
        return "SELECT t.*, 
                (/* Базовый вес */ 1 + 
                /* Вес предпочтений */ IF(JSON_CONTAINS(:preferred_cats, CAST(t.category_id AS JSON)), 2, 0) - 
                /* Вес игнорируемых тегов */ IFNULL((
                    SELECT COUNT(*) FROM thread_tags tt 
                    JOIN tags ON tt.tag_id = tags.id
                    WHERE tt.thread_id = t.id 
                    AND JSON_CONTAINS(:ignored_tags, JSON_QUOTE(tags.name))
                ), 0) * 0.5 +
                /* Вес истории просмотров */ IFNULL((
                    SELECT LOG(SUM(view_duration) / 100 FROM user_view_stats 
                    WHERE user_id = :user_id AND thread_id = t.id
                ), 0)) as relevance
                FROM threads t
                ORDER BY relevance DESC";
    }
}