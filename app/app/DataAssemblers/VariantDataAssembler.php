<?php

namespace App\DataAssemblers;

/**
 * Class VariantDataAssembler
 * @package App\DataAssemblers
 */
class VariantDataAssembler
{
    /**
     * @param array $data
     * @return array
     */
    public function make(array $data): array
    {
        return [
            'id'         => $data['id'],
            'isComplete' => $data['is_complete'],
            'expiredAt'  => $data['expired_at'] ? strtotime($data['expired_at']) : null,
            'finishedAt' => $data['finished_at'] ? strtotime($data['finished_at']) : null,
            'question'   => json_decode($data['question']),
            'result'     => $data['result'] ? json_decode($data['result']) : null,
        ];
    }
}
