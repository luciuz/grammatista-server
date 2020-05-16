<?php

namespace App\Validators;

/**
 * Class ListValidator
 * @package App\Validators
 */
class ListValidator extends AbstractValidator
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
