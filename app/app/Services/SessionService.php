<?php

namespace App\Services;

use Illuminate\Redis\RedisManager;

/**
 * Class SessionService
 * @package App\Services
 */
class SessionService
{
    private const SESSION_PREFIX = 'session:';

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
     * @return string
     * @throws \Exception
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * @param string $token
     * @param array  $data
     */
    public function createSession(string $token, array $data): void
    {
        $this->redis->set($this->getKey($token), json_encode($data));
    }

    /**
     * @param string $token
     * @return array
     */
    public function findSession(string $token): ?array
    {
        $data = $this->redis->get($this->getKey($token));
        return json_decode($data, true);
    }

    /**
     * @param string $token
     * @return string
     */
    private function getKey(string $token): string
    {
        return self::SESSION_PREFIX . $token;
    }
}
