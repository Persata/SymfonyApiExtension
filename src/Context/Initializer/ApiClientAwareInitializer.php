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
     * @inheritDoc
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritDoc
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof ApiClientAwareContext) {
            return;
        }

        $context->setApiClient($this->apiClient);
    }
}