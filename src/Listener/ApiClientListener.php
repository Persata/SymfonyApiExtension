<?php

namespace Persata\SymfonyApiExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Persata\SymfonyApiExtension\ApiClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ApiClientListener
 *
 * @package Persata\SymfonyApiExtension\Listener
 */
class ApiClientListener implements EventSubscriberInterface
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
    public static function getSubscribedEvents(): array
    {
        return [
            ScenarioTested::AFTER => ['resetApiClient', 10],
            ExampleTested::AFTER  => ['resetApiClient', 10],
        ];
    }

    public function resetApiClient()
    {
        $this->apiClient->reset();
    }
}
