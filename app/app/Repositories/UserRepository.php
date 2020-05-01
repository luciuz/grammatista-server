<?php

namespace App\Repositories;

use App\Models\User;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::query()->where('id', $id)->first();
    }
}
