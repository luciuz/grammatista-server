<?php

namespace App\Repositories;

use App\Models\Test;

/**
 * Class TestRepository
 * @package App\Repositories
 */
class TestRepository
{
    /**
     * @param int $id
     * @return Test|null
     */
    public function findById(int $id): ?Test
    {
        return Test::query()->where('id', $id)->first();
    }

    /**
     * @param array $attributes
     * @return Test
     */
    public function create(array $attributes): Test
    {
        $model = new Test($attributes);
        $model->save();
        return $model;
    }
}
