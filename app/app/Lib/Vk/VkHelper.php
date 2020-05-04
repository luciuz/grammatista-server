<?php

namespace App\Lib\Vk;

/**
 * Class VkService
 * @package App\Lib\Vk
 */
class VkHelper
{
    /** @var string */
    private $clientSecret;

    /**
     * @param string $clientSecret
     */
    public function __construct(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param array  $params
     * @param string $sign
     * @return bool
     */
    public function checkSign(array $params, string $sign): bool
    {
        return $this->generateSign($params) === $sign;
    }

    /**
     * @param array $params
     * @return string
     */
    public function generateSign(array $params): string
    {
        $signParams = [];
        foreach ($params as $name => $value) {
            if (strpos($name, 'vk_') !== 0) {
                continue;
            }

            $signParams[$name] = $value;
        }
        ksort($signParams);
        $query = http_build_query($signParams);
        return rtrim(strtr(base64_encode(hash_hmac('sha256', $query, $this->clientSecret, true)), '+/', '-_'), '=');
    }
}
