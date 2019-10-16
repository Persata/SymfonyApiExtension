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
                bootstrap: config/bootstrap.php
                kernel:
                    class: App\Kernel
                    path: src/Kernel.php
                    environment: test
                    debug: true
    ```

## Additional Custom Contexts

This package provides a `ContextInitializer` that will set the shared `ApiClient` on any Behat Contexts that implement the `ApiClientAwareContext`. It is best to extend the `RawApiContext` as this already includes the getter & setter for the `ApiClient` instance.


## FOSRest Form Validation

If you are using the FOSRestBundle within your Symfony application and are making use of the [form response features](https://symfony.com/doc/master/bundles/FOSRestBundle/2-the-view-layer.html#forms-and-views), there is an additional `FOSRestFormValidationContext` class provided for testing for the presence or omission of errors:

```gherkin
Then the JSON response should have the error "This value should not be null." at "user.firstName"
And the JSON response should not have any errors on "user.lastName"
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
    And the JSON response should have the structure
    """
    [
        "hello"
    ]
    """
```

## Extension Configuration
```yaml
# behat.yml
default:
    extensions:
        Persata\SymfonyApiExtension:
            base_url: 'http://my-api.local' # Base URL used for requests sent to the Symfony kernel. Defaults to null.
            files_path: "%paths.base%/src/Behat/Resources/fixtures/" # Base directory of files to be used for upload tests. Defaults to null.
```
