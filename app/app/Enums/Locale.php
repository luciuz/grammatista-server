<?php

namespace App\Enums;

/**
 * Class Locale
 * @package App\Enums
 */
class Locale
{
    public const RU = 'ru';
    public const EN = 'en';

    /**
     * @return array|string[]
     */
    public function getAll(): array
    {
        return [
            self::RU,
            self::EN,
        ];
    }
}
