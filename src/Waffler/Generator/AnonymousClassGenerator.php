<?php

declare(strict_types = 1);

namespace Waffler\Generator;

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
     * @phpstan-var array<string, TFactoryFunction>
     * @phpstan-template TFactoryFunction of \Waffler\Generator\FactoryFunction
     */
    private static array $cache = [];

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function instantiate(
        string $interfaceName,
        MethodCallHandler $methodCallHandler
    ): object {
        return $this->instantiateFromReflection(
            new ReflectionClass($interfaceName),
            $methodCallHandler
        );
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function instantiateFromReflection(
        ReflectionClass $reflectionInterface,
        MethodCallHandler $methodCallHandler
    ): object {
        if (!$reflectionInterface->isInterface()) {
            throw new \InvalidArgumentException("The type is not an interface");
        }

        $factoryFunction = $this->getFactoryFunction($reflectionInterface);

        return $factoryFunction($methodCallHandler);
    }

    /**
     * Retrieves the factory function that generates the anonymous class.
     *
     * @param \ReflectionClass<TInterfaceType> $reflectionInterface
     *
     * @return \Waffler\Generator\FactoryFunction<TInterfaceType>
     * @throws \Exception
     * @phpstan-template TInterfaceType of object
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getFactoryFunction(\ReflectionClass $reflectionInterface): FactoryFunction
    {
        // @phpstan-ignore-next-line
        return self::$cache[$reflectionInterface->name]
            ??= new FactoryFunction($this->evaluateClosure($reflectionInterface));
    }

    /**
     * Generates the closure that creates the anonymous class implementation of the interface.
     *
     * @param \ReflectionClass<TInterfaceType> $reflectionInterface
     *
     * @return \Closure(MethodCallHandler): TInterfaceType
     * @throws \Exception
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @phpstan-template TInterfaceType of object
     */
    private function evaluateClosure(\ReflectionClass $reflectionInterface): \Closure
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
     * @param array<\ReflectionMethod> $reflectionMethods
     *
     * @return string
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getMethodsRepresentation(array $reflectionMethods): string
    {
        $methods = [];

        foreach ($reflectionMethods as $method) {
            $methods[] = new MethodCompiler($method);
        }

        return implode(PHP_EOL, $methods);
    }
}