<?php
namespace App\Models;

use Core\Database;
use Core\Cache\RedisCache;

class Subscription {
    private Database $db;
    private RedisCache $cache;

    public function __construct() {
        $this->db = new Database();
        $this->cache = new RedisCache();
    }

    public function subscribe(
        int $userId,
        string $targetType,
        int $targetId,
        array $notificationPrefs = ['email' => true, 'push' => true]
    ): bool {
        $this->cache->invalidate("user_{$userId}_subscriptions");
        
        return $this->db->query(
            "INSERT INTO subscriptions 
             (user_id, target_type, target_id, notification_prefs)
             VALUES (:user_id, :target_type, :target_id, :prefs)
             ON DUPLICATE KEY UPDATE notification_prefs = VALUES(notification_prefs)",
            [
                'user_id' => $userId,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'prefs' => json_encode($notificationPrefs)
            ]
        )->rowCount() > 0;
    }

    public function getSubscribers(
        string $targetType,
        int $targetId,
        string $notificationType = null
    ): array {
        $cacheKey = "subscribers_{$targetType}_{$targetId}";
        
        return $this->cache->get($cacheKey, function() use ($targetType, $targetId, $notificationType) {
            $sql = "SELECT user_id, notification_prefs FROM subscriptions 
                    WHERE target_type = :target_type AND target_id = :target_id";
            
            if ($notificationType) {
                $sql .= " AND JSON_EXTRACT(notification_prefs, '$.{$notificationType}') = true";
            }
            
            return $this->db->query($sql, [
                'target_type' => $targetType,
                'target_id' => $targetId
            ])->fetchAll();
        }, 600);
    }
}