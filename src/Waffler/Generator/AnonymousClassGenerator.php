<?php

namespace Waffler\Generator;

use ReflectionClass;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class ClassGenerator.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template TInterfaceType of object
 * @template-implements \Waffler\Generator\Contracts\InterfaceInstantiator<TInterfaceType>
 */
class AnonymousClassGenerator implements InterfaceInstantiator
{
    /**
     * @var array<string, \Waffler\Generator\FactoryFunction<TInterfaceType>>
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
     * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getFactoryFunction(\ReflectionClass $reflectionInterface): FactoryFunction
    {
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
     * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function evaluateClosure(\ReflectionClass $reflectionInterface): \Closure
    {
        $anonymousClassGenerator = sprintf(
            'return fn($handler) => new class($handler) implements %s {
            public function __construct(private $handler) {}
            
            private function _callHandler(string $method, array $arguments)
            {
                return $this->handler->{$method}(...$arguments);
            }
            
            %s
        };',
            '\\' . $reflectionInterface->name,
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
            $methods[] = (string)(new MethodCompiler($method));
        }

        return implode("\n", $methods);
    }
}