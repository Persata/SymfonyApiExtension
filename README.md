# Symfony API Extension [![License](https://img.shields.io/packagist/l/persata/symfony-api-extension.svg)](https://packagist.org/packages/persata/symfony-api-extension) [![Version](https://img.shields.io/packagist/v/persata/symfony-api-extension.svg)](https://packagist.org/packages/persata/symfony-api-extension) [![Build Status](https://travis-ci.org/Persata/SymfonyApiExtension.svg?branch=master)](https://travis-ci.org/Persata/SymfonyApiExtension)

Provides functionality for testing Symfony APIs with Behat.

This package offers an `ApiClient` class that can be shared between all relevant contexts that require being able to modify the request or inspect the response. 

Requires the FriendsOfBehat [SymfonyExtension](https://github.com/FriendsOfBehat/SymfonyExtension).

## Usage

1. Install via Composer:

    ```bash
    $ composer require persata/symfony-api-extension --dev
    ```

2. Enable and configure in your Behat configuration:

    ```yaml
    # behat.yml
    default:
        suites:
            default:
                contexts:
                    - Persata\SymfonyApiExtension\Context\ApiContext: ~

        extensions:
            Persata\SymfonyApiExtension: ~
            FriendsOfBehat\SymfonyExtension:
                kernel:
                    bootstrap: vendor/autoload.php
    ```

## Additional Custom Contexts

This package provides a `ContextInitializer` that will set the shared `ApiClient` on any Behat Contexts that implement the `ApiClientAwareContext`. It is best to extend the `RawApiContext` as this already includes the getter & setter for the `ApiClient` instance.

## ContextServiceExtension Integration

If using the FriendsOfBehat [ContextServiceExtension](https://github.com/FriendsOfBehat/ContextServiceExtension), declare the `ApiContext` service like so:

```yaml
services:
    # ...
    persata.symfony_api_extension.context.api_context:
        class: Persata\SymfonyApiExtension\Context\ApiContext
        calls:
            - [setApiClient, ['@__behat__.api_client']]
        tags:
            - { name: fob.context_service }
```

If you need additional context services with access to the same shared ApiClient instance, have them extend `RawApiContext` and then declare the service like so:

```yaml
services:
    # ...
    the_id.of.your.custom_context:
        class: TheClass\Of\Your\CustomContext
        calls:
            - [setApiClient, ['@__behat__.api_client']]
        tags:
            - { name: fob.context_service }
```

## Example Scenario
```gherkin
Scenario: Testing Basic JSON Endpoint
    Given the "Authorization" request header is "Bearer MyToken"
    When the request is sent using GET to "/example-endpoint"
    Then the response status code should be 200
    And the response content type should be JSON
    And the response content should be valid JSON
    And the JSON response should be
    """
    {
        "hello": "world"
    }
    """
```