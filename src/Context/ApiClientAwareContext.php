<?php

namespace Persata\SymfonyApiExtension\Context;

use Behat\Behat\Context\Context;
use Persata\SymfonyApiExtension\ApiClient;

/**
 * Interface ApiClientAwareContext
 *
 * @package Persata\SymfonyApiExtension\Context
 */
interface ApiClientAwareContext extends Context
{
    /**
     * @param ApiClient $apiClient
     * @return ApiClientAwareContext
     */
    public function setApiClient(ApiClient $apiClient);

    /**
     * @param array $parameters
     * @return ApiClientAwareContext
     */
    public function setApiExtensionParameters(array $parameters);
}
