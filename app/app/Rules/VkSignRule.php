<?php

namespace App\Rules;

use App\Services\Vk\VkService;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class VkSignRule
 * @package App\Rules
 */
class VkSignRule implements Rule
{
    /** @var VkService */
    private $vkHelper;

    /** @var array */
    private $params;

    /**
     * @param VkService $vkHelper
     */
    public function __construct(VkService $vkHelper)
    {
        $this->vkHelper = $vkHelper;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     */
    public function passes($attribute, $value): bool
    {
        return $this->vkHelper->checkSign($this->params, $value);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return trans('validation.custom.invalid_sign');
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
