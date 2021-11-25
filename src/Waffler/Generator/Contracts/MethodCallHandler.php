<?php

namespace Waffler\Generator\Contracts;

/**
 * Interface MethodCallHandler.
 *
 * Receives all method calls from the anonymous implementation of the interface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface MethodCallHandler
{
    /**
     * Handle the method calls.
     *
     * @param string            $method
     * @param array<int, mixed> $arguments
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function __call(string $method, array $arguments): mixed;
}