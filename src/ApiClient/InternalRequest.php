<?php

namespace Persata\SymfonyApiExtension\ApiClient;

use Symfony\Component\BrowserKit\Request;

/**
 * Class InternalRequest
 *
 * @package Persata\SymfonyApiExtension\ApiClient
 */
class InternalRequest extends Request
{
    /**
     * @param string|null $baseUrl
     * @return InternalRequest
     */
    public static function createDefault(string $baseUrl = null): InternalRequest
    {
        return new static($baseUrl, null);
    }

    /**
     * @param mixed $content
     * @return InternalRequest
     */
    public function setContent($content): InternalRequest
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return InternalRequest
     */
    public function setServerParameter($key, $value): InternalRequest
    {
        $this->server[$key] = $value;
        return $this;
    }
}