<?php

class RedisOnlySessionHandler implements SessionHandlerInterface
{
    private $redis;

    public function open(string $path, string $name): bool
    {   
        $endpoint = 'redis';
        $port = 6379;
        $timeout = 5.0;
        $readTimeout = 5.0; 

        // お手製リトライ
        // gethostbynamelを使ってエンドポイントから全てのIPアドレスを取得
        $ipAddresses = gethostbynamel($endpoint);
        if ($ipAddresses === false) {
            die("Failed to resolve the endpoint: $endpoint");
        }
        foreach ($ipAddresses as $ip) {
            $node = [$ip . ':' . $port];
            try {
                error_log("Redis connecting to $ip");
                $redis = new RedisCluster(NULL, $node, $timeout, $readTimeout);
                $this->redis = $redis;
                break;
            } catch (Exception $e) {
                // 接続に失敗したらエラーをログに記録し、再接続を試みる
                error_log("Redis connection to $ip failed: " . $e->getMessage());
                sleep(1); 
            }
        }
        if (!isset($redis)) {
            die('Failed to connect to all resolved IP addresses');
            return false;
        }

        // RedisCluster実装リトライ
        // $nodes = array_map(function($ip) {
        //     return $ip . ':6379';
        // }, $ipAddresses);
        // $redis = new RedisCluster(NULL, $nodes, $timeout, $readTimeout);
        // $redis->setOption(\RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_DISTRIBUTE);
        $this->redis = $redis;
        
        return true;
    }

    public function close(): bool
    {
        $this->redis->close();
        return true;
    }

    public function read(string $id): string|false
    {
        $data = $this->redis->get($id);
        return $data === false ? '' : $data;
    }

    public function write(string $id, string $data): bool
    {
        $this->redis->set($id, $data);
        return true;
    }

    public function destroy(string $id): bool
    {
        $this->redis->del($id);
        return true;
    }

    public function gc(int $maxlifetime): int|false
    {
        // Redisではガベージコレクションが不要
        return true;
    }
}

