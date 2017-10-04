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
     * @return ApiClientAwareContext
     */
    public function setApiClient(ApiClient $apiClient): ApiClientAwareContext
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }
}