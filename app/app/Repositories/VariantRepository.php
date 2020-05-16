<?php

namespace App\Repositories;

use App\Models\Bookmark;
use App\Models\Lesson;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

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
     * @param int $id
     * @param int $userId
     * @return array|null
     */
    public function findRichById(int $id, int $userId): ?array
    {
        $variant = (new Variant())->getTable();
        $lesson = (new Lesson())->getTable();
        $query = \DB::table($variant)
            ->leftJoin($lesson, static function (JoinClause $join) use ($lesson, $variant) {
                $join->on($lesson . '.id', '=', $variant . '.lesson_id')
                    ->whereNull($lesson . '.deleted_at')
                    ->where($lesson . '.published_at', '<', Carbon::now());
            })
            ->where([
                $variant . '.id' => $id,
                $variant . '.user_id' => $userId
            ])
            ->selectRaw(<<<SQL
                $variant.*,
                $lesson.title
SQL);
        $result = $query->first();
        return $result ? (array) $result : null;
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

    /**
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return array
     */
    public function list(?int $maxId, int $userId, int $size): array
    {
        $lesson = (new Lesson())->getTable();
        $variant = (new Variant())->getTable();
        $query = $this->listQuery($maxId, $userId, $size)
            ->selectRaw(<<<SQL
                count(*) over() as total_rows,
                $variant.id id,
                $lesson.id lesson_id,
                $lesson.title,
                $variant.is_complete
SQL);
        return $query->get()->all();
    }

    /**
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return Builder
     */
    private function listQuery(?int $maxId, int $userId, int $size): Builder
    {
        $lesson = (new Lesson())->getTable();
        $variant = (new Variant())->getTable();

        $query = \DB::table($variant)
            ->leftJoin($lesson, static function (JoinClause $join) use ($lesson, $variant) {
                $join->on($lesson . '.id', '=', $variant . '.lesson_id')
                    ->whereNull($lesson . '.deleted_at')
                    ->where($lesson . '.published_at', '<', Carbon::now());
            })
            ->where($variant . '.user_id', $userId)
            ->whereNull($variant . '.deleted_at')
            ->whereNotNull($variant . '.finished_at')
            ->orderBy($variant . '.id', 'desc')
            ->limit($size);

        if ($maxId !== null) {
            $query->where($variant . '.id', '<', $maxId);
        }
        return $query;
    }
}
