<?php

namespace App\Repositories;

use App\Models\Variant;

/**
 * Class VariantRepository
 * @package App\Repositories
 */
class VariantRepository
{
    /**
     * @param int $id
     * @return Variant|null
     */
    public function findById(int $id): ?Variant
    {
        return Variant::query()->where('id', $id)->first();
    }

    /**
     * @param array $attributes
     * @return Variant
     */
    public function create(array $attributes): Variant
    {
        $model = new Variant($attributes);
        $model->save();
        return $model;
    }

    /**
     * @param Variant $model
     * @param array   $attributes
     * @return Variant
     */
    public function update(Variant $model, array $attributes): Variant
    {
        foreach ($attributes as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return $model;
    }
}
