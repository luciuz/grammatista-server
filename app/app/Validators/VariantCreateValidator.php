<?php

namespace App\Validators;

use App\Rules\UuidV4Rule;
use Illuminate\Validation\Factory as ValidationFactory;

/**
 * Class VariantCreateValidator
 * @package App\Validators
 */
class VariantCreateValidator extends AbstractValidator
{
    private const LESSON_ID = 'lessonId';
    private const TRANSACTION_TOKEN = 'transactionToken';

    /** @var UuidV4Rule */
    private $uuidV4Rule;

    /**
     * @param ValidationFactory $validator
     * @param UuidV4Rule        $uuidV4Rule
     */
    public function __construct(
        ValidationFactory $validator,
        UuidV4Rule $uuidV4Rule
    ) {
        parent::__construct($validator);
        $this->uuidV4Rule = $uuidV4Rule;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::LESSON_ID         => 'required|int',
            self::TRANSACTION_TOKEN => ['required', 'string', $this->uuidV4Rule],
        ];
    }
}
