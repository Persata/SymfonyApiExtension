<?php

namespace Persata\SymfonyApiExtension\Context;

use Persata\SymfonyApiExtension\ApiClient;
use Webmozart\Assert\Assert;

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
     * @var array
     */
    private $apiExtensionParameters;

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
     * @param array $parameters
     * @return ApiClientAwareContext
     */
    public function setApiExtensionParameters(array $parameters): ApiClientAwareContext
    {
        $this->apiExtensionParameters = $parameters;
        return $this;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getApiExtensionParameter($name)
    {
        return $this->apiExtensionParameters[$name] ?? null;
    }

    /**
     * @param array $expectedJsonStructure
     * @param array $responseJson
     */
    protected function assertJsonStructure($expectedJsonStructure, $responseJson)
    {
        foreach ($expectedJsonStructure as $key => $value) {
            if (is_array($value)) {
                if ($key === '*') {
                    Assert::isArray($responseJson);
                    foreach ($responseJson as $responseJsonItem) {
                        $this->assertJsonStructure($expectedJsonStructure['*'], $responseJsonItem);
                    }
                } else {
                    Assert::keyExists($responseJson, $key);
                    $this->assertJsonStructure($expectedJsonStructure[$key], $responseJson[$key]);
                }
            } else {
                Assert::keyExists($responseJson, $value);
            }
        }
    }
}
