<?php

namespace App\Repositories;

use App\Models\TransactionToken;

/**
 * Class TransactionTokenRepository
 * @package App\Repositories
 */
class TransactionTokenRepository
{
    /**
     * @param array $attributes
     * @return TransactionToken
     */
    public function create(array $attributes): TransactionToken
    {
        $model = new TransactionToken($attributes);
        $model->save();
        return $model;
    }

    /**
     * @param string     $token
     * @param array|null $result
     * @return TransactionToken
     */
    public function createByTokenWithResult(string $token, ?array $result): TransactionToken
    {
        return $this->create([
            'transaction_token' => $token,
            'result'            => $result,
        ]);
    }

    /**
     * @param string $token
     * @return TransactionToken|null
     */
    public function findByToken(string $token): ?TransactionToken
    {
        return TransactionToken::query()->where('transaction_token', $token)->first();
    }
}
