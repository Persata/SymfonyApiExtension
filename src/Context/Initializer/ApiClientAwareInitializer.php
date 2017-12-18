<?php

namespace Persata\SymfonyApiExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Persata\SymfonyApiExtension\ApiClient;
use Persata\SymfonyApiExtension\Context\ApiClientAwareContext;

/**
 * Class ApiClientAwareInitializer
 *
 * @package Persata\SymfonyApiExtension\Context\Initializer
 */
class ApiClientAwareInitializer implements ContextInitializer
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var array
     */
    private $apiExtensionParameters;

    /**
     * ApiClientAwareInitializer constructor.
     *
     * @param ApiClient $apiClient
     * @param array     $parameters
     */
    public function __construct(ApiClient $apiClient, array $parameters = [])
    {
        $this->apiClient = $apiClient;
        $this->apiExtensionParameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function initializeContext(Context $context)
    {
        if (! $context instanceof ApiClientAwareContext) {
            return;
        }

        $context->setApiClient($this->apiClient);
        $context->setApiExtensionParameters($this->apiExtensionParameters);
    }
}
