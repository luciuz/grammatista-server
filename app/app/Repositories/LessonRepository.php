<?php

namespace App\Repositories;

use App\Models\Bookmark;
use App\Models\Lesson;
use App\Models\Variant;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * Class LessonRepository
 * @package App\Repositories
 */
class LessonRepository
{
    /**
     * @param int $id
     * @return Lesson|null
     */
    public function findById(int $id): ?Lesson
    {
        return Lesson::query()->where('id', $id)->first();
    }

    /**
     * @param array $attributes
     * @return Lesson|null
     */
    public function create(array $attributes): ?Lesson
    {
        $model = new Lesson($attributes);
        if ($model->save()) {
            return $model;
        }

        return null;
    }

    /**
     * @param string   $q
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return array
     */
    public function search(string $q, ?int $maxId, int $userId, int $size): array
    {
        $lesson = (new Lesson())->getTable();
        $bookmark = (new Bookmark())->getTable();
        $variant = (new Variant())->getTable();
        $query = $this->searchQuery($q, $maxId, $userId, $size)
            ->selectRaw(<<<SQL
                count(*) over() as total_rows,
                $lesson.id,
                $lesson.title,
                $bookmark.id bookmark_id,
                $variant.id complete_id
SQL);
            return $query->get()->all();
    }

    /**
     * @param string   $q
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return Builder
     */
    public function searchQuery(string $q, ?int $maxId, int $userId, int $size): Builder
    {
        $lesson = (new Lesson())->getTable();
        $bookmark = (new Bookmark())->getTable();
        $variant = (new Variant())->getTable();

        $query = \DB::table($lesson)
            ->leftJoin($bookmark, static function (JoinClause $join) use ($bookmark, $lesson, $userId) {
                $join->on($bookmark . '.lesson_id', '=', $lesson . '.id')
                    ->where($bookmark . '.user_id', $userId);
            })
            ->leftJoin($variant, static function (JoinClause $join) use ($variant, $lesson, $userId) {
                $join->on($variant . '.lesson_id', '=', $lesson . '.id')
                    ->where($variant . '.user_id', $userId)
                    ->where($variant . '.is_complete', true)
                    ->whereNull($variant . '.deleted_at');
            })
            ->where($lesson . '.title', 'ilike', '%' . $q . '%')
            ->whereNull($lesson . '.deleted_at')
            ->orderBy($lesson . '.id', 'desc')
            ->limit($size);
        if ($maxId !== null) {
            $query->where($lesson . '.id', '<', $maxId);
        }
        return $query;
    }
}
