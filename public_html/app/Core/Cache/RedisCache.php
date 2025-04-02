<?php
namespace Core\Cache;

use Redis;
use Core\Exceptions\CacheException;

class RedisCache {
    private Redis $redis;
    private string $prefix;

    public function __construct(string $host = '127.0.0.1', int $port = 6379, string $prefix = 'forum_') {
        $this->redis = new Redis();
        $this->prefix = $prefix;
        
        if (!$this->redis->connect($host, $port)) {
            throw new CacheException("Could not connect to Redis");
        }
    }

    public function get(string $key, callable $callback = null, int $ttl = 3600): mixed {
        $fullKey = $this->prefix . $key;
        
        if ($this->redis->exists($fullKey)) {
            return unserialize($this->redis->get($fullKey));
        }
        
        if ($callback) {
            $value = $callback();
            $this->set($key, $value, $ttl);
            return $value;
        }
        
        return null;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        return $this->redis->setex(
            $this->prefix . $key,
            $ttl,
            serialize($value)
        );
    }

    public function invalidate(string $key): bool {
        return $this->redis->del($this->prefix . $key) > 0;
    }
}
// Пример в ForumController
public function index() {
    $cache = new \Core\Cache();
    $categories = $cache->get('forum_categories', function() {
        return (new \App\Models\Forum\Category())->getAllWithThreadsCount();
    }, 3600);
    
    View::render('forum/index.php', ['categories' => $categories]);
}