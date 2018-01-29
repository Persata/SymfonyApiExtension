<?php

namespace Persata\SymfonyApiExtension\Tests;

use Persata\SymfonyApiExtension\ApiClient;
use Persata\SymfonyApiExtension\Context\ApiContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonResponseTest
 *
 * @package Persata\SymfonyApiExtension\Tests
 */
class JsonResponseTest extends TestCase
{
    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var ApiClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockApiClient;

    /**
     * @var Response|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockResponse;

    public function setUp()
    {
        $this->mockResponse = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockApiClient = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockApiClient
            ->method('getResponse')
            ->willReturn($this->mockResponse);

        $this->apiContext = (new ApiContext())
            ->setApiClient($this->mockApiClient);
    }

    public function testJsonResponseKey()
    {
        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'firstName' => 'Ross',
                'lastName'  => 'Kinsman',
            ]));

        $this->apiContext->theJSONResponseShouldHaveTheKeyEqualTo('firstName', 'Ross');
        $this->apiContext->theJSONResponseShouldHaveTheKeyEqualTo('lastName', 'Kinsman');
    }

    public function testNestedJsonResponseKeyDefaultSeparator()
    {
        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'data' => [
                    'user' => [
                        'firstName' => 'Ross',
                        'lastName'  => 'Kinsman',
                    ],
                ],
            ]));

        $this->apiContext->theJSONResponseShouldHaveTheNestedKeyEqualTo('data.user.firstName', 'Ross');
        $this->apiContext->theJSONResponseShouldHaveTheNestedKeyEqualTo('data.user.lastName', 'Kinsman');
    }

    public function testNestedJsonResponseKeyCustomSeparator()
    {
        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'data' => [
                    'user' => [
                        'firstName' => 'Ross',
                        'lastName'  => 'Kinsman',
                    ],
                ],
            ]));

        $this->apiContext->theJSONResponseShouldHaveTheNestedKeyEqualTo('data:user:firstName', 'Ross', ':');
        $this->apiContext->theJSONResponseShouldHaveTheNestedKeyEqualTo('data:user:lastName', 'Kinsman', ':');
    }
}
