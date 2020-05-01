<?php

namespace App\Validators;

/**
 * Class LessonSearchValidator
 * @package App\Validators
 */
class LessonSearchValidator extends AbstractValidator
{
    private const F_Q = 'q';

    /**
     * @return array
     *
     */
    public function rules(): array
    {
        return [
            self::F_Q => 'required|string|min:3|max:255',
        ];
    }
}
