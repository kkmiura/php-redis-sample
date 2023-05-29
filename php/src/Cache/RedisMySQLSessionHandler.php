<?php

class RedisMySQLSessionHandler implements SessionHandlerInterface
{
    private $redis;
    private $db;

    public function __construct()
    {
        $dsn = 'mysql:host=mysql;dbname=development_db;charset=utf8mb4';
        $db_user = 'root';
        $db_pass = 'mysql';

        $endpoint = 'redis';
        $port = 6379;
        $timeout = 5.0;
        $readTimeout = 5.0;
        $node = [$endpoint . ':' . $port];
        
        // DBの接続
        try {
            $db = new PDO($dsn, $db_user, $db_pass);
            $this->db = $db;
        } catch (PDOException $e) {
            error_log("MySQL connection is failed: " . $e->getMessage());
        }

        // Redisの接続
        try {
            $redis = new RedisCluster(NULL, $node, $timeout, $readTimeout);
            $this->redis = $redis;
        } catch (Exception $e) {
            error_log("Redis connection is failed: " . $e->getMessage());
        }
    }

    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        try {
            $res = false;
            if ($this->redis) {
                $res = $this->redis->get($id);
            }
            
            if ($res === false) {
                $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = ?");
                $stmt->execute([$id]);
                $res = $stmt->fetchColumn();
            }
            
            if ($this->redis) {
                $this->redis->set($id, $res);
            }
        } catch (Exception $e) {
            error_log("Redis connection is failed: " . $e->getMessage());
        }
        
        return $res === false ? '' : $res;
    }

    public function write(string $id, string $data): bool
    {
        if ($this->db) {
            $stmt = $this->db->prepare("REPLACE INTO sessions (id, data) VALUES (?, ?)");
            $stmt->execute([$id, $data]);
        }
            
        if ($this->redis) {
            $this->redis->set($id, $data);
        }
        return true;
    }

    public function destroy(string $id): bool
    {
        if ($this->db) {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$id]);
        }

        if ($this->redis) {
            $this->redis->del($id);
        }
        return true;
    }

    public function gc(int $maxlifetime): int|false
    {
        if ($this-db) {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE timestamp < ?");
            $stmt->execute([time() - $maxlifetime]);
        }
        // Redisのガベージコレクションは自動
        return true;
    }
}
