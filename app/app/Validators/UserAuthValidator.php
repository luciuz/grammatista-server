<?php

namespace App\Validators;

use App\Rules\UuidV4Rule;
use App\Rules\VkSignRule;
use Illuminate\Validation\Factory as ValidationFactory;

/**
 * Class UserAuthValidator
 * @package App\Validators
 */
class UserAuthValidator extends AbstractValidator
{
    public const VK_USER_ID        = 'vk_user_id';
    public const SIGN              = 'sign';
    public const TRANSACTION_TOKEN = 'transaction_token';

    /** @var VkSignRule */
    private $vkSignRule;

    /** @var UuidV4Rule */
    private $uuidV4Rule;

    /**
     * @param ValidationFactory $validator
     * @param VkSignRule        $vkSignRule
     * @param UuidV4Rule        $uuidV4Rule
     */
    public function __construct(
        ValidationFactory $validator,
        VkSignRule $vkSignRule,
        UuidV4Rule $uuidV4Rule
    ) {
        parent::__construct($validator);
        $this->vkSignRule = $vkSignRule;
        $this->uuidV4Rule = $uuidV4Rule;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::VK_USER_ID              => 'required|int',
            self::SIGN                    => ['required', 'string', $this->vkSignRule],
            self::TRANSACTION_TOKEN       => ['required', 'string', $this->uuidV4Rule],
        ];
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data): void
    {
        $this->vkSignRule->setParams($data); // set rule params
        parent::validate($data);
    }
}
