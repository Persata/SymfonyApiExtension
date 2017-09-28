<?php

namespace Persata\SymfonyApiExtension;

use Persata\SymfonyApiExtension\ApiClient\InternalRequest;
use Persata\SymfonyApiExtension\Exception\UnknownHeaderException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class ApiClient
 *
 * @package Persata\SymfonyApiExtension
 */
class ApiClient
{
    /**
     * @var array
     */
    private static $headerToServerMap = [
        'Accept'        => 'HTTP_ACCEPT',
        'Authorization' => 'HTTP_AUTHORIZATION',
        'Content-Type'  => 'CONTENT_TYPE',
    ];

    /**
     * @var InternalRequest
     */
    protected $internalRequest;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var bool
     */
    private $hasPerformedRequest;

    /**
     * ApiClient constructor.
     *
     * @param Kernel      $kernel
     * @param string|null $baseUrl
     */
    public function __construct(Kernel $kernel, string $baseUrl = null)
    {
        $this->kernel = $kernel;
        $this->baseUrl = $baseUrl;
        $this->internalRequest = InternalRequest::createDefault($this->baseUrl);
    }

    /**
     * Reset the internal state of the API client
     */
    public function reset(): ApiClient
    {
        $this->internalRequest = InternalRequest::createDefault($this->baseUrl);
        $this->request = null;
        $this->response = null;
        return $this;
    }

    /**
     * @return InternalRequest
     */
    public function getInternalRequest(): InternalRequest
    {
        return $this->internalRequest;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @throws UnknownHeaderException
     */
    public function setRequestHeader($key, $value)
    {
        if (array_key_exists($key, self::$headerToServerMap)) {
            $this->setServerParameter(self::$headerToServerMap[$key], $value);
            return;
        }

        throw new UnknownHeaderException($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setServerParameter($key, $value)
    {
        $this->internalRequest->setServerParameter($key, $value);
    }

    /**
     * @param string $requestBody
     */
    public function setRequestBody(string $requestBody = null)
    {
        $this->internalRequest->setContent($requestBody);
    }

    /**
     * @param string $method
     * @param string $uri
     */
    public function request(string $method, string $uri)
    {
        $this->request = Request::create(
            $uri,
            $method,
            $this->internalRequest->getParameters(),
            $this->internalRequest->getCookies(),
            $this->internalRequest->getFiles(),
            $this->internalRequest->getServer(),
            $this->internalRequest->getContent()
        );

        if ($this->hasPerformedRequest) {
            $this->kernel->shutdown();
        } else {
            $this->hasPerformedRequest = true;
        }

        $this->response = $this->kernel->handle($this->request);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}