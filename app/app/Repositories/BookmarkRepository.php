<?php

namespace App\Repositories;

use App\Models\Bookmark;

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
}
