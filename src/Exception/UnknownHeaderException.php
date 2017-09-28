<?php

namespace Persata\SymfonyApiExtension\Exception;

/**
 * Class UnknownHeaderException
 *
 * @package Persata\SymfonyApiExtension\Exception
 */
class UnknownHeaderException extends \InvalidArgumentException
{
    /**
     * @inheritDoc
     */
    public function __construct($headerKey)
    {
        parent::__construct(sprintf("Unknown header -> server key '%s' specified, please use `setServerParameter` directly.", $headerKey));
    }
}