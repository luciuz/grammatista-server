<?php

namespace App\Validators;

/**
 * Class LessonSearchValidator
 * @package App\Validators
 */
class LessonSearchValidator extends AbstractValidator
{
    private const Q      = 'q';
    private const MAX_ID = 'maxId';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::Q      => 'required|string|max:255',
            self::MAX_ID => 'nullable|integer|min:1',
        ];
    }
}
