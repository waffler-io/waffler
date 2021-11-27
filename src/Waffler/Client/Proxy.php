<?php

namespace Waffler\Client;

use BadMethodCallException;
use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class Proxy.
 *
 * This class proxies all calls form the anonymous implementation to the real method.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-template TInterfaceType of object
 */
class Proxy implements MethodCallHandler
{
    /**
     * @param \ReflectionClass<TInterfaceType> $interface
     * @param \Waffler\Client\MethodInvoker    $methodInvoker
     */
    public function __construct(
        private \ReflectionClass $interface,
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
}