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
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Utils\NestedResource;
use Waffler\Client\Readers\MethodReader;
use Waffler\Client\Traits\InteractsWithAttributes;
use Waffler\Generator\Contracts\MethodCallHandler;

use function Waffler\arrayWrap;

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
    use InteractsWithAttributes;

    /**
     * @param \ReflectionClass<TInterfaceType> $interface
     * @param \Waffler\Client\MethodInvoker    $methodInvoker
     * @param array<string, mixed>             $options
     */
    public function __construct(
        private ReflectionClass $interface,
        private MethodInvoker $methodInvoker,
        private array $options = []
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

        $method = $this->interface->getMethod($name);

        if ($this->reflectionHasAttribute($method, NestedResource::class)) {
            return $this->buildNestedResource($method, $arguments);
        }

        return $this->methodInvoker->invokeMethod(
            $method,
            $arguments,
            arrayWrap($this->options['waffler_client_path_prefix'] ?? [])
        );
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

    /**
     * @param \ReflectionMethod        $reflectionMethod
     * @param array<int|string, mixed> $arguments
     *
     * @return object
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function buildNestedResource(\ReflectionMethod $reflectionMethod, array $arguments): object
    {
        $returnType = $reflectionMethod->getReturnType();

        if (!$returnType instanceof \ReflectionNamedType) {
            throw new BadMethodCallException("Nested resource methods must return an interface type.");
        }

        $options = $this->options;
        $interfaceName = $returnType->getName();

        if ($this->reflectionHasAttribute($reflectionMethod, Path::class)) {
            $methodReader = new MethodReader($reflectionMethod, $arguments);
            $options['waffler_client_path_prefix'] = explode('/', $methodReader->parsePath());
        } else {
            $options['waffler_client_path_prefix'] = [];
        }

        return Factory::make($interfaceName, $options); //@phpstan-ignore-line
    }
}
