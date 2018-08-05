<?php

namespace Persata\SymfonyApiExtension\Tests;

use Behat\Gherkin\Node\PyStringNode;
use InvalidArgumentException;
use Persata\SymfonyApiExtension\ApiClient;
use Persata\SymfonyApiExtension\Context\ApiContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonStructureTest
 *
 * @package Persata\SymfonyApiExtension\Tests
 */
class JsonStructureTest extends TestCase
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

    /**
     * @return void
     */
    public function testSimpleJsonStructure()
    {
        $simpleStructureNode = new PyStringNode([
            "[
                'firstName',
                'lastName',
            ]",
        ], 0);

        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'firstName' => 'Ross',
                'lastName'  => 'Kinsman',
            ]));

        $this->apiContext->theJSONResponseShouldHaveTheStructure($simpleStructureNode);
    }

    /**
     * @return void
     */
    public function testSimpleJsonStructureFail()
    {
        $simpleStructureNode = new PyStringNode([
            "[
                'firstName',
                'lastName',
            ]",
        ], 0);

        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'firstName' => 'Ross',
            ]));

        $this->expectException(InvalidArgumentException::class);

        try {
            $this->apiContext->theJSONResponseShouldHaveTheStructure($simpleStructureNode);
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->assertEquals('Expected the key "lastName" to exist.', $invalidArgumentException->getMessage());
            throw $invalidArgumentException;
        }
    }

    /**
     * @return void
     */
    public function testNestedJsonStructure()
    {
        $nestedStructureNode = new PyStringNode([
            "[
                'firstName',
                'occupation' => [
                    'jobTitle',
                    'companyName',
                ],
            ]",
        ], 0);


        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'firstName'  => 'Ross',
                'occupation' => [
                    'jobTitle'    => 'Web Developer',
                    'companyName' => 'The Drum',
                ],
            ]));

        $this->apiContext->theJSONResponseShouldHaveTheStructure($nestedStructureNode);
    }

    /**
     * @return void
     */
    public function testNestedJsonStructureFail()
    {
        $nestedStructureNode = new PyStringNode([
            json_encode([
                'firstName',
                'occupation' => [
                    'jobTitle',
                    'companyName',
                ],
            ]),
        ], 0);


        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                'firstName'  => 'Ross',
                'occupation' => [
                    'jobTitle' => 'Web Developer',
                ],
            ]));

        $this->expectException(InvalidArgumentException::class);

        try {
            $this->apiContext->theJSONResponseShouldHaveTheStructure($nestedStructureNode);
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->assertEquals('Expected the key "companyName" to exist.', $invalidArgumentException->getMessage());
            throw $invalidArgumentException;
        }
    }

    /**
     * @return void
     */
    public function testAsteriskStructure()
    {
        $asteriskStructureNode = new PyStringNode([
            "[
                '*' => [
                    'id',
                    'firstName',
                    'lastName',
                ],
            ]",
        ], 0);


        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                [
                    'id'        => 11,
                    'firstName' => 'Ross',
                    'lastName'  => 'Kinsman',
                ],
            ]));

        $this->apiContext->theJSONResponseShouldHaveTheStructure($asteriskStructureNode);
    }

    /**
     * @return void
     */
    public function testAsteriskStructureFail()
    {
        $asteriskStructureNode = new PyStringNode([
            "[
                '*' => [
                    'id',
                    'firstName',
                    'lastName',
                ],
            ]",
        ], 0);


        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                [
                    'id'        => 11,
                    'firstName' => 'Ross',
                ],
            ]));

        $this->expectException(InvalidArgumentException::class);

        try {
            $this->apiContext->theJSONResponseShouldHaveTheStructure($asteriskStructureNode);
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->assertEquals('Expected the key "lastName" to exist.', $invalidArgumentException->getMessage());
            throw $invalidArgumentException;
        }
    }

    /**
     * @return void
     */
    public function testAsteriskStructureFailBackwardsCompatibility()
    {
        $asteriskStructureNode = new PyStringNode([
            json_encode([
                '*' => [
                    'id',
                    'firstName',
                    'lastName',
                ],
            ]),
        ], 0);


        $this->mockResponse
            ->method('getContent')
            ->willReturn(json_encode([
                [
                    'id'        => 11,
                    'firstName' => 'Ross',
                ],
            ]));

        $this->expectException(InvalidArgumentException::class);

        try {
            $this->apiContext->theJSONResponseShouldHaveTheStructure($asteriskStructureNode);
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->assertEquals('Expected the key "lastName" to exist.', $invalidArgumentException->getMessage());
            throw $invalidArgumentException;
        }
    }
}
