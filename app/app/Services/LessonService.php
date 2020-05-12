<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\Lesson;
use App\Models\Variant;
use App\Repositories\LessonRepository;
use Illuminate\Database\Query\JoinClause;

/**
 * Class LessonService
 * @package App\Services
 */
class LessonService
{
    private const SEARCH_SIZE = 50;

    /** @var LessonRepository */
    private $lessonRepository;

    /**
     * @param LessonRepository $lessonRepository
     */
    public function __construct(LessonRepository $lessonRepository)
    {
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * @return LessonRepository
     */
    public function getRepository(): LessonRepository
    {
        return $this->lessonRepository;
    }

    /**
     * @param string   $q
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return array
     */
    public function search(string $q, ?int $maxId, int $userId, int $size = self::SEARCH_SIZE): array
    {
        $raw = $this->lessonRepository->search($q, $maxId, $userId, $size);

        $list = [];
        $rowsLeft = null;
        $maxId = null;
        if (count($raw) > 0) {
            $totalRows = reset($raw)->total_rows;
            $rowsLeft = $totalRows - count($raw);
            $maxId = last($raw)->id;
            foreach ($raw as $item) {
                $list[] = [
                    'id'         => $item->id,
                    'title'      => $item->title,
                    'isBookmark' => (bool) $item->bookmark_id,
                    'isComplete' => (bool) $item->complete_id,
                ];
            }
        }
        return compact('list', 'rowsLeft', 'maxId');
    }

    /**
     * @param int $id
     * @param int $userId
     * @return array|null
     */
    public function getRichById(int $id, int $userId): ?array
    {
        $raw = $this->lessonRepository->getRichById($id, $userId);
        if ($raw === null) {
            return null;
        }

        return [
            'id'         => $raw['id'],
            'title'      => $raw['title'],
            'body'       => json_decode($raw['body']),
            'isBookmark' => (bool) $raw['bookmark_id'],
            'isComplete' => (bool) $raw['complete_id'],
        ];
    }
}
