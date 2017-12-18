<?php

namespace Persata\SymfonyApiExtension;

use Persata\SymfonyApiExtension\ApiClient\InternalRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * Class ApiClient
 *
 * @package Persata\SymfonyApiExtension
 */
class ApiClient
{
    /**
     * List of headers not prefixed with 'HTTP_'
     */
    const NON_HTTP_PREFIXED_HEADERS = [
        'Content-Type',
        'Content-Length',
        'Content-MD5',
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
     * @var array
     */
    private $parameters;

    /**
     * @var bool
     */
    private $profiler = false;

    /**
     * ApiClient constructor.
     *
     * @param Kernel      $kernel
     * @param string|null $baseUrl
     */
    public function __construct(Kernel $kernel, string $baseUrl = null, array $parameters = [])
    {
        $this->kernel = $kernel;
        $this->baseUrl = $baseUrl;
        $this->parameters = $parameters;
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
     * @param string $value
     * @return ApiClient
     */
    public function setRequestHeader($key, $value): ApiClient
    {
        if (\in_array($key, self::NON_HTTP_PREFIXED_HEADERS, false)) {
            $key = str_replace('-', '_', strtoupper($key));
        } else {
            $key = sprintf('HTTP_%s', preg_replace('/\s|-/', '_', strtoupper($key)));
        }

        return $this->setServerParameter($key, $value);
    }

    /**
     * @param string $key
     * @param string $value
     * @return ApiClient
     */
    public function setServerParameter(string $key, $value): ApiClient
    {
        $this->internalRequest->setServerParameter($key, $value);
        return $this;
    }

    /**
     * @param string $requestBody
     * @return ApiClient
     */
    public function setRequestBody(string $requestBody = null): ApiClient
    {
        $this->internalRequest->setContent($requestBody);
        return $this;
    }

    /**
     * @param array $requestParameters
     * @return ApiClient
     */
    public function setRequestParameters(array $requestParameters = []): ApiClient
    {
        $this->internalRequest->setParameters($requestParameters);
        return $this;
    }

    /**
     * @param string $path
     * @param string $requestKey
     * @return ApiClient
     */
    public function addFile(string $path, string $requestKey)
    {
        $this->internalRequest->addFile($path, $requestKey);
        return $this;
    }

    /**
     * @param string $method
     * @param string $uri
     * @return ApiClient
     */
    public function request(string $method, string $uri): ApiClient
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

        if ($this->profiler) {
            $this->profiler = false;

            $this->kernel->boot();
            $this->kernel->getContainer()->get('profiler')->enable();
        }

        $this->response = $this->kernel->handle($this->request);

        if ($this->kernel instanceof TerminableInterface) {
            $this->kernel->terminate($this->request, $this->response);
        }

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Gets the profile associated with the current Response.
     *
     * @return HttpProfile|bool A Profile instance
     */
    public function getProfile()
    {
        if (! $this->kernel->getContainer()->has('profiler')) {
            return false;
        }

        return $this->kernel->getContainer()->get('profiler')->loadProfileFromResponse($this->response);
    }

    /**
     * Enables the profiler for the very next request.
     *
     * If the profiler is not enabled, the call to this method does nothing.
     */
    public function enableProfiler()
    {
        if ($this->kernel->getContainer()->has('profiler')) {
            $this->profiler = true;
        }
    }
}
