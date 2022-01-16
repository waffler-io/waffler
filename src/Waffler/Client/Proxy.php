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

        // If the method has the "NestedResource" attribute, it means that the return type is another interface
        // that the user wants to instantiate. The "child" interface will inherit the parent guzzle http options
        // plus the extra path if it has in the method parameters. All others parameters will be ignored except
        // "PathParam".
        if ($this->reflectionHasAttribute($method, NestedResource::class)) {
            return $this->buildNestedResource($method, $arguments);
        }

        // If the method doesn't have the "NestedResource" attribute, we will just make the request using the arguments
        // and the attributes of the method and parameters.
        return $this->methodInvoker->invokeMethod(
            $method,
            $arguments,
            arrayWrap($this->options['waffler_client_path_prefix'] ?? []) // Extra path if it has.
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
     * Builds an interface using the parent options and extra a extra path from the method arguments if it has.
     *
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

        // If the method does not have a return type or is not a type of reflection named type, we will throw an error.
        // If the method return type is not an interface, the Factory::make() on the last line of this method will
        // handle this properly.
        if (!$returnType instanceof \ReflectionNamedType) {
            throw new BadMethodCallException("Nested resource methods must return an interface type.");
        }

        $options = $this->options;
        $options['waffler_client_path_prefix'] ??= [];
        $interfaceName = $returnType->getName();

        // If the method has A "Path" attribute, it means that the "nested" interface must inherit the parent base_uri
        // plus this extra path. We will build the path, using the path params if it has.
        if ($this->reflectionHasAttribute($reflectionMethod, Path::class)) {
            $methodReader = new MethodReader($reflectionMethod, $arguments);

            // If the parent is another nested resource, we need to merge the parent path with this new path.
            $options['waffler_client_path_prefix'] = [
                ...$options['waffler_client_path_prefix'],
                ...array_filter(
                    explode('/', $methodReader->parsePath()),
                    fn ($element) => $element !== ''
                )
            ];
        }

        // Finally, build the nested resource interface.
        return Factory::make($interfaceName, $options); //@phpstan-ignore-line
    }
}
