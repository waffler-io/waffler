<?php

namespace Waffler\Waffler\Implementation\Exceptions;

use RuntimeException;
use Throwable;

class NotAnInterfaceException extends RuntimeException
{
    private const MESSAGE = '[%s] is not an interface. Cannot generate implementation.';

    public function __construct(string $target, Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $target), 1, $previous);
    }
}