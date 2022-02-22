<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client\Exceptions;

use ReflectionMethod;
use RuntimeException;

/**
 * Class MethodIsNotBatchedException.
 *
 * Thrown when the method is not batched.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class MethodIsNotBatchedException extends RuntimeException
{
    public function __construct(private ReflectionMethod $reflectionMethod)
    {
        parent::__construct("The method [{$this->reflectionMethod->getName()}] is not batched.");
    }

    /**
     * @return \ReflectionMethod
     */
    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }
}
