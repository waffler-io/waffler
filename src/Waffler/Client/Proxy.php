<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client;

use BadMethodCallException;
use ReflectionClass;
use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class Proxy.
 *
 * This class proxies all calls form the anonymous implementation to the real method.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-template TInterfaceType of object
 * @implements MethodCallHandler<TInterfaceType>
 */
class Proxy implements MethodCallHandler
{
    /**
     * @param \ReflectionClass<TInterfaceType> $interface
     * @param \Waffler\Client\MethodInvoker    $methodInvoker
     */
    public function __construct(
        private ReflectionClass $interface,
        private MethodInvoker $methodInvoker
    ) {
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!$this->interface->hasMethod($name)) {
            $this->throwUndefinedMethodError($name);
        }

        return $this->methodInvoker->invokeMethod($this->interface->getMethod($name), $arguments);
    }

    /**
     * @param string $name
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function throwUndefinedMethodError(string $name): void
    {
        throw new BadMethodCallException(
            "The method $name is not declared in {$this->interface->getName()}."
        );
    }

    /**
     * @inheritDoc
     * @return \ReflectionClass<TInterfaceType>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getReflectedInterface(): ReflectionClass
    {
        return $this->interface;
    }
}
