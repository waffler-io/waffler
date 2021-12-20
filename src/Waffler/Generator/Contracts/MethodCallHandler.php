<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Generator\Contracts;

use ReflectionClass;

/**
 * Interface MethodCallHandler.
 *
 * Receives all method calls from the anonymous implementation of the interface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template TInterface of object
 */
interface MethodCallHandler
{
    /**
     * Handle the method calls.
     *
     * @param string            $name
     * @param array<int, mixed> $arguments
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function __call(string $name, array $arguments): mixed;

    /**
     * Retrieves the interface that is reflected.
     *
     * @return \ReflectionClass<TInterface>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getReflectedInterface(): ReflectionClass;
}
