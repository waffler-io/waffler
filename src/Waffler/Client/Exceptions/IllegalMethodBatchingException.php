<?php

namespace Waffler\Waffler\Client\Exceptions;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use ReflectionMethod;
use RuntimeException;

/**
 * Class IllegalMethodBatchingException.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class IllegalMethodBatchingException extends RuntimeException
{
    public function __construct(private ReflectionMethod $reflectionMethod)
    {
        parent::__construct("Cannot batch a batched method.");
    }

    /**
     * @return \ReflectionMethod
     */
    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }
}
