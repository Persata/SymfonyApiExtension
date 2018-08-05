<?php

namespace Persata\SymfonyApiExtension\Tests;

use Persata\SymfonyApiExtension\ApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class HeadersTest
 *
 * @package Persata\SymfonyApiExtension\Tests
 */
class HeadersTest extends TestCase
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    protected function setUp()
    {
        /** @var Kernel | \PHPUnit_Framework_MockObject_MockObject $kernelMock */
        $kernelMock = $this->createMock(Kernel::class);
        $this->apiClient = new ApiClient($kernelMock, 'http://localhost/');
    }

    public function testContentType()
    {
        $this->apiClient->setRequestHeader('Content-Type', 'application/json');

        $serverParameters = $this->apiClient->getInternalRequest()->getServer();

        $this->assertArrayHasKey('CONTENT_TYPE', $serverParameters);
        $this->assertEquals('application/json', $serverParameters['CONTENT_TYPE']);
    }

    public function testAccept()
    {
        $this->apiClient->setRequestHeader('Accept', 'application/json');

        $serverParameters = $this->apiClient->getInternalRequest()->getServer();

        $this->assertArrayHasKey('HTTP_ACCEPT', $serverParameters);
        $this->assertEquals('application/json', $serverParameters['HTTP_ACCEPT']);
    }

    public function testAuthorization()
    {
        $this->apiClient->setRequestHeader('Authorization', 'Bearer MyToken');

        $serverParameters = $this->apiClient->getInternalRequest()->getServer();

        $this->assertArrayHasKey('HTTP_AUTHORIZATION', $serverParameters);
        $this->assertEquals('Bearer MyToken', $serverParameters['HTTP_AUTHORIZATION']);
    }

    public function testContentTransferEncoding()
    {
        $this->apiClient->setRequestHeader('Content-Transfer-Encoding', 'BINARY');

        $serverParameters = $this->apiClient->getInternalRequest()->getServer();

        $this->assertArrayHasKey('HTTP_CONTENT_TRANSFER_ENCODING', $serverParameters);
        $this->assertEquals('BINARY', $serverParameters['HTTP_CONTENT_TRANSFER_ENCODING']);
    }
}
