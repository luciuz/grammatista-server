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
            'id'        => $data['id'],
            'expiredAt' => $data['expired_at'] ? $data['expired_at']->timestamp : null,
            'question'  => $data['question'],
            'result'    => $data['result'],
        ];
    }
}
