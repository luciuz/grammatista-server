<?php

namespace App\Services;

use App\Repositories\BookmarkRepository;

/**
 * Class BookmarkService
 * @package App\Services
 */
class BookmarkService
{
    private const LIST_SIZE = 50;

    /** @var BookmarkRepository */
    private $bookmarkRepository;

    /**
     * @param BookmarkRepository $bookmarkRepository
     */
    public function __construct(BookmarkRepository $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * @return BookmarkRepository
     */
    public function getRepository(): BookmarkRepository
    {
        return $this->bookmarkRepository;
    }

    /**
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return array
     */
    public function list(?int $maxId, int $userId, int $size = self::LIST_SIZE): array
    {
        $raw = $this->bookmarkRepository->list($maxId, $userId, $size);

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
                    'lessonId'   => $item->lesson_id,
                    'title'      => $item->title,
                    'isComplete' => (bool) $item->complete_id,
                ];
            }
        }
        return compact('list', 'rowsLeft', 'maxId');
    }
}
