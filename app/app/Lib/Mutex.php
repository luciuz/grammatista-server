<?php

namespace App\Lib;

use Illuminate\Redis\RedisManager;

/**
 * Class Mutex
 * @package App\Lib
 */
class Mutex
{
    private const REDIS_KEY_PREFIX = 'mutex:';

    /** @var RedisManager */
    private $redis;

    /**
     * @param RedisManager $redis
     */
    public function __construct(RedisManager $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $name
     * @param int    $timeout
     * @param int    $ttl
     * @return bool
     */
    public function acquireLock(string $name, int $timeout = 0, int $ttl = 0): bool
    {
        $waitTime = 0;
        while ($this->redis->setnx($this->getRedisKey($name), 1) !== 1) {
            $waitTime++;
            if ($waitTime > $timeout) {
                return false;
            }
            sleep(1);
        }
        if ($ttl) {
            $this->redis->expire($this->getRedisKey($name), $ttl);
        }
        return true;
    }

    /**
     * @param string $name
     */
    public function releaseLock(string $name): void
    {
        $this->redis->del($this->getRedisKey($name));
    }

    /**
     * @param string $name
     * @return string
     */
    private function getRedisKey(string $name): string
    {
        return static::REDIS_KEY_PREFIX . md5($name);
    }
}
