<?php

namespace App\Validators;

use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Factory as ValidationFactory;

/**
 * Class AbstractValidator
 * @package App\Validators
 */
abstract class AbstractValidator
{
    /**
     * @var ValidationFactory
     */
    private $validator;

    /**
     * @param ValidationFactory $validator
     */
    public function __construct(ValidationFactory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $this->validator->validate($data, $this->rules(), $this->messages(), $this->customAttributes());
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function customAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    abstract public function rules(): array;
}
