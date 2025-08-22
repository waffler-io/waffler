<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Exceptions;

use RuntimeException;
use Throwable;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

class NotAnInterfaceException extends RuntimeException implements GeneratorExceptionInterface
{
    private const MESSAGE = '[%s] is not an interface. Cannot generate implementation.';

    public function __construct(string $target, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $target), 1, $previous);
    }
}
