<?php

namespace App\Validators;

/**
 * Class BookmarkListValidator
 * @package App\Validators
 */
class BookmarkListValidator extends AbstractValidator
{
    private const MAX_ID = 'maxId';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::MAX_ID => 'nullable|integer|min:1',
        ];
    }
}
