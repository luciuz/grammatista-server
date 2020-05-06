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
        $totalRows = null;
        $maxId = null;
        if (count($raw) > 0) {
            $totalRows = reset($raw)->total_rows;
            $maxId = last($raw)->id;
            foreach ($raw as $item) {
                $list[] = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'is_bookmark' => (bool) $item->bookmark_id,
                    'is_complete' => (bool) $item->complete_id,
                ];
            }
        }
        return compact('list', 'totalRows', 'maxId');
    }
}
