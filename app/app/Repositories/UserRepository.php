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

    /**
     * @param int $vkId
     * @return User|null
     */
    public function findByVkId(int $vkId): ?User
    {
        return User::query()->where('vk_id', $vkId)->first();
    }

    /**
     * @param array $attributes
     * @return User|null
     */
    public function create(array $attributes): ?User
    {
        $user = new User($attributes);
        if ($user->save()) {
            return $user;
        }

        return null;
    }
}
