<?php

namespace App\Validators;

/**
 * Class GetValidator
 * @package App\Validators
 */
class GetValidator extends AbstractValidator
{
    private const ID = 'id';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::ID => 'required|int',
        ];
    }
}
