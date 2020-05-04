<?php

namespace App\Services\Idempotent;

use App\Lib\Mutex;
use App\Repositories\TransactionTokenRepository;

/**
 * Class IdempotentService
 * @package App\Services\Idempotent
 */
class IdempotentService
{
    /**
     * Maximum waiting time in seconds
     */
    private const MUTEX_TIMEOUT = 5;

    /**
     * Maximum locking time in seconds
     */
    private const MUTEX_TTL = 30;

    /** @var Mutex */
    private $mutex;

    /** @var TransactionTokenRepository */
    private $repository;

    /**
     * @param Mutex                      $mutex
     * @param TransactionTokenRepository $repository
     */
    public function __construct(Mutex $mutex, TransactionTokenRepository $repository)
    {
        $this->mutex = $mutex;
        $this->repository = $repository;
    }

    /**
     * Run $callable as idempotent
     * @param string   $transactionToken
     * @param callable $callable
     * @param array    $params
     * @return array|null
     * @throws \Throwable
     */
    public function runIdempotent(string $transactionToken, callable $callable, array $params): ?array
    {
        if (!$this->mutex->acquireLock($transactionToken, static::MUTEX_TIMEOUT, static::MUTEX_TTL)) {
            throw new IdempotentMutexException('Can not acquire lock. Method has already started.');
        }

        try {
            if ($model = $this->repository->findByToken($transactionToken)) {
                return $model->result;
            }

            $result = $callable(...$params);
            $this->repository->createByTokenWithResult($transactionToken, $result);
            return $result;
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            $this->mutex->releaseLock($transactionToken);
        }
    }
}
