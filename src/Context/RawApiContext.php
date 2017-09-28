<?php

namespace Persata\SymfonyApiExtension\Context;

use Persata\SymfonyApiExtension\ApiClient;

/**
 * Class RawApiContext
 *
 * @package Persata\SymfonyApiExtension\Context
 */
class RawApiContext implements ApiClientAwareContext
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }
}