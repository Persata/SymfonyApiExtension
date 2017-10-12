<?php

namespace Persata\SymfonyApiExtension\Tests;

use Persata\SymfonyApiExtension\ApiClient;
use Persata\SymfonyApiExtension\Context\FOSRestFormValidationContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FOSRestFormValidationTest
 *
 * @package Persata\SymfonyApiExtension\Tests
 */
class FOSRestFormValidationTest extends TestCase
{
    /**
     * @var FOSRestFormValidationContext
     */
    private $fosRestFormValidationContext;

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

        $this->fosRestFormValidationContext = (new FOSRestFormValidationContext())
            ->setApiClient($this->mockApiClient);
    }

    public function testErrorAtFirstLevel()
    {
        $errorMessage = 'This value should not be null.';

        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'code',
                'message',
                'errors' => [
                    'children' => [
                        'firstName' => [
                            'errors' => [
                                $errorMessage,
                            ],
                        ],
                    ],
                ],
            ]));

        $this->fosRestFormValidationContext->theJSONResponseShouldHaveTheErrorAt($errorMessage, 'firstName');
    }

    public function testErrorAtNestedLevel()
    {
        $errorMessage = 'This value should not be null.';

        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'code',
                'message',
                'errors' => [
                    'children' => [
                        'user' => [
                            'children' => [
                                'profile' => [
                                    'children' => [
                                        'firstName' => [
                                            'errors' => [
                                                $errorMessage,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]));

        $this->fosRestFormValidationContext->theJSONResponseShouldHaveTheErrorAt($errorMessage, 'user.profile.firstName');
    }

    public function testNoErrorAtFirstLevel()
    {
        $errorMessage = 'This value should not be null.';

        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'code',
                'message',
                'errors' => [
                    'children' => [
                        'user' => [
                            'children' => [
                                'profile' => [
                                    'children' => [
                                        'firstName' => [
                                            'errors' => [
                                                $errorMessage,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]));

        $this->fosRestFormValidationContext->theJSONResponseShouldNotHaveAnyErrorsOn('user');
    }
}