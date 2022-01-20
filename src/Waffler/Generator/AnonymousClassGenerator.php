<?php

declare(strict_types=1);

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Generator;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class ClassGenerator.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class AnonymousClassGenerator implements InterfaceInstantiator
{
    /**
     * @psalm-var array<string, \Waffler\Generator\FactoryFunction>
     */
    private static array $cache = [];

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function instantiate(MethodCallHandler $methodCallHandler): object
    {
        $reflectedInterface = $methodCallHandler->getReflectedInterface();

        if (!$reflectedInterface->isInterface()) {
            throw new InvalidArgumentException("The type is not an interface", 10);
        }

        $factoryFunction = $this->getFactoryFunction($reflectedInterface);

        return $factoryFunction($methodCallHandler);
    }

    /**
     * Retrieves the factory function that generates the anonymous class.
     *
     * @psalm-param \ReflectionClass<TInterfaceType> $reflectionInterface
     *
     * @return \Waffler\Generator\FactoryFunction<TInterfaceType>
     * @throws \Exception
     * @psalm-template TInterfaceType of object
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getFactoryFunction(ReflectionClass $reflectionInterface): FactoryFunction
    {
        return self::$cache[$reflectionInterface->name]
            ??= new FactoryFunction($this->createClosure($reflectionInterface));
    }

    /**
     * Generates the closure that creates the anonymous class implementation of the interface.
     *
     * @psalm-param \ReflectionClass<TInterfaceType> $reflectionInterface
     *
     * @return \Closure(MethodCallHandler<TInterfaceType>): TInterfaceType
     * @throws \Exception
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @psalm-template TInterfaceType of object
     */
    private function createClosure(ReflectionClass $reflectionInterface): Closure
    {
        $anonymousClassGenerator = sprintf(
            'return fn($handler) => new class($handler) implements %s {
                public function __construct(private $handler) {}
                %s
            };',
            "\\{$reflectionInterface->name}",
            $this->getMethodsRepresentation($reflectionInterface->getMethods())
        );

        return eval($anonymousClassGenerator);
    }

    /**
     * Retrieves the methods in its string representation.
     *
     * @psalm-param array<\ReflectionMethod> $reflectionMethods
     *
     * @return string
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getMethodsRepresentation(array $reflectionMethods): string
    {
        $methods = [];

        foreach ($reflectionMethods as $method) {
            $methods[] = new AnonymousClassMethod($method);
        }

        return implode(PHP_EOL, $methods);
    }
}
