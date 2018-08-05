<?php

namespace Persata\SymfonyApiExtension\ApiClient;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class InternalRequest
 *
 * @package Persata\SymfonyApiExtension\ApiClient
 */
class InternalRequest extends Request
{
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
     * @param array $parameters
     * @return InternalRequest
     */
    public function setParameters(array $parameters): InternalRequest
    {
        $this->parameters = $parameters;
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

    /**
     * @param string $path
     * @param string $requestKey
     * @return InternalRequest
     */
    public function addFile(string $path, string $requestKey): InternalRequest
    {
        $this->files[$requestKey] = $this->getUploadedFileInstance($path);

        return $this;
    }

    /**
     * @param string $path
     * @param string $requestKey
     * @return InternalRequest
     */
    public function addFileToArray(string $path, string $requestKey): InternalRequest
    {
        if (! isset($this->files[$requestKey]) || ! \is_array($this->files[$requestKey])) {
            $this->files[$requestKey] = [];
        }

        $this->files[$requestKey][] = $this->getUploadedFileInstance($path);

        return $this;
    }

    /**
     * @param string $path
     * @return UploadedFile
     */
    protected function getUploadedFileInstance(string $path): UploadedFile
    {
        if (null !== $path && is_readable($path)) {
            $error = UPLOAD_ERR_OK;
            $size = filesize($path);
            $info = pathinfo($path);
            $name = $info['basename'];

            // copy to a tmp location
            $tmp = sys_get_temp_dir() . '/' . sha1(uniqid(mt_rand(), true));
            if (array_key_exists('extension', $info)) {
                $tmp .= '.' . $info['extension'];
            }
            if (is_file($tmp)) {
                unlink($tmp);
            }
            copy($path, $tmp);
            $path = $tmp;
        } else {
            $error = UPLOAD_ERR_NO_FILE;
            $size = 0;
            $name = '';
            $path = '';
        }

        return new UploadedFile($path, $name, '', $size, $error, true);
    }

}
