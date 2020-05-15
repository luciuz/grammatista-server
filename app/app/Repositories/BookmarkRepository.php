<?php

namespace App\Repositories;

use App\Models\Bookmark;
use App\Models\Lesson;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * Class BookmarkRepository
 * @package App\Repositories
 */
class BookmarkRepository
{
    /**
     * @param int $id
     * @return Bookmark|null
     */
    public function findById(int $id): ?Bookmark
    {
        return Bookmark::query()->where('id', $id)->first();
    }

    /**
     * @param array $attributes
     * @return Bookmark
     */
    public function create(array $attributes): Bookmark
    {
        $model = new Bookmark($attributes);
        $model->save();
        return $model;
    }

    /**
     * @param array $attributes
     */
    public function delete(array $attributes): void
    {
        Bookmark::query()->where($attributes)->delete();
    }

    /**
     * @param int $userId
     * @param int $lessonId
     * @return bool
     */
    public function existsByUserIdLessonId(int $userId, int $lessonId): bool
    {
        return Bookmark::query()->where([
            'user_id'   => $userId,
            'lesson_id' => $lessonId,
        ])->exists();
    }

    /**
     * @param int $userId
     * @param int $lessonId
     * @return Bookmark
     */
    public function createByUserIdLessonId(int $userId, int $lessonId): Bookmark
    {
        return $this->create([
            'user_id'   => $userId,
            'lesson_id' => $lessonId,
        ]);
    }

    /**
     * @param int $userId
     * @param int $lessonId
     */
    public function deleteByUserIdLessonId(int $userId, int $lessonId): void
    {
        $this->delete([
            'user_id'   => $userId,
            'lesson_id' => $lessonId,
        ]);
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
        $bookmark = (new Bookmark())->getTable();
        $variant = (new Variant())->getTable();
        $query = $this->listQuery($maxId, $userId, $size)
            ->selectRaw(<<<SQL
                count(*) over() as total_rows,
                $bookmark.id id,
                $lesson.id lesson_id,
                $lesson.title,
                COUNT($variant.id) complete_id
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
        $bookmark = (new Bookmark())->getTable();
        $variant = (new Variant())->getTable();

        $query = \DB::table($lesson)
            ->join($bookmark, static function (JoinClause $join) use ($bookmark, $lesson, $userId) {
                $join->on($bookmark . '.lesson_id', '=', $lesson . '.id')
                    ->where($bookmark . '.user_id', $userId);
            })
            ->leftJoin($variant, static function (JoinClause $join) use ($variant, $lesson, $userId) {
                $join->on($variant . '.lesson_id', '=', $lesson . '.id')
                    ->where($variant . '.user_id', $userId)
                    ->where($variant . '.is_complete', true)
                    ->whereNull($variant . '.deleted_at');
            })
            ->groupBy([$bookmark . '.id', $lesson . '.id'])
            ->whereNull($lesson . '.deleted_at')
            ->where($lesson . '.published_at', '<', Carbon::now())
            ->orderBy($bookmark . '.id', 'desc')
            ->limit($size);
        if ($maxId !== null) {
            $query->where($bookmark . '.id', '<', $maxId);
        }
        return $query;
    }
}
