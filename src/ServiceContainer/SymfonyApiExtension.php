<?php

namespace Persata\SymfonyApiExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Exception\ExtensionInitializationException;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Persata\SymfonyApiExtension\ApiClient;
use Persata\SymfonyApiExtension\Context\Initializer\ApiClientAwareInitializer;
use Persata\SymfonyApiExtension\Listener\ApiClientListener;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SymfonyApiExtension
 *
 * @package Persata\SymfonyApiExtension\ServiceContainer
 */
class SymfonyApiExtension implements ExtensionInterface
{
    /**
     * Container ID of the API client class
     */
    const API_CLIENT_ID = 'api_client';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey(): string
    {
        return 'symfony_api';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        $symfonyExtension = $extensionManager->getExtension('fob_symfony');

        if (null === $symfonyExtension) {
            throw new ExtensionInitializationException(sprintf('The %s extension must be enabled for this extension to function.', SymfonyExtension::class), $this->getConfigKey());
        }
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('base_url')->defaultValue('http://localhost/')->end()
                ->scalarNode('files_path')->defaultNull()->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $container->setDefinition(self::API_CLIENT_ID, new Definition(ApiClient::class, [
            new Reference(SymfonyExtension::DRIVER_KERNEL_ID),
            $config['base_url']
        ]));

        $this->loadContextInitializer($container);
        $this->loadApiClientListener($container);

        $container->setParameter('symfony_api_extension.parameters', $config);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition(ApiClientAwareInitializer::class, [
            new Reference(self::API_CLIENT_ID),
            '%symfony_api_extension.parameters%'
        ]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $container->setDefinition('symfony_api.context_initializer', $definition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadApiClientListener(ContainerBuilder $container)
    {
        $definition = new Definition(ApiClientListener::class, [
            new Reference(self::API_CLIENT_ID),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition('symfony_api.listener.api_client', $definition);
    }
}
