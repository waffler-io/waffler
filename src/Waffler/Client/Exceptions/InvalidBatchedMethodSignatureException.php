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

/**
 * Class InvalidBatchedMethodSignatureException.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class InvalidBatchedMethodSignatureException extends \RuntimeException
{
    public const REASON_ARGS = 'The method must have exacly one argument and must be of type array.';
    public const REASON_RETURN_TYPE = 'The method must return an array or a promise.';

    public function __construct(private ReflectionMethod $reflectionMethod, private string $reason)
    {
        parent::__construct("The method [{$this->reflectionMethod->getName()}] has invalid signature. Reason: {$this->reason}");
    }

    /**
     * @return \ReflectionMethod
     */
    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
