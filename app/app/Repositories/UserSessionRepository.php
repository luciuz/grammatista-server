<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;

/**
 * Class UserSessionRepository
 * @package App\Repositories
 */
class UserSessionRepository
{
    /**
     * @param string $token
     * @return User|null
     */
    public function findByToken(string $token): ?UserSession
    {
        return UserSession::query()
            ->where('token', $token)
            ->where('expired_at', '>', Carbon::now())
            ->first();
    }

    /**
     * @param array $attributes
     * @return UserSession|null
     */
    public function create(array $attributes): ?UserSession
    {
        $model = new UserSession($attributes);
        if ($model->save()) {
            return $model;
        }

        return null;
    }
}
