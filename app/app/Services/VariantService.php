<?php

namespace App\Services;

use App\Repositories\VariantRepository;

/**
 * Class VariantService
 * @package App\Services
 */
class VariantService
{
    private const SEARCH_SIZE = 50;

    /** @var VariantRepository */
    private $variantRepository;

    /**
     * @param VariantRepository $variantRepository
     */
    public function __construct(VariantRepository $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }


    /**
     * @return VariantRepository
     */
    public function getRepository(): VariantRepository
    {
        return $this->variantRepository;
    }

    /**
     * @param int|null $maxId
     * @param int      $userId
     * @param int      $size
     * @return array
     */
    public function list(?int $maxId, int $userId, int $size = self::SEARCH_SIZE): array
    {
        $raw = $this->variantRepository->list($maxId, $userId, $size);

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
                    'isComplete' => $item->is_complete,
                    'finishedAt' => strtotime($item->finished_at),
                ];
            }
        }
        return compact('list', 'rowsLeft', 'maxId');
    }
}
