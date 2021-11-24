<?php

namespace Waffler\Generator\Contracts;

use ReflectionClass;

/**
 * Interface InterfaceInstantiator.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template TInterfaceType of object
 */
interface InterfaceInstantiator
{
    /**
     * Instantiate an interface by its name.
     *
     * @param class-string<TInterfaceType>|\ReflectionClass<TInterfaceType> $interfaceNameOrReflection
     * @param \Waffler\Generator\Contracts\MethodCallHandler                $methodCallHandler
     *
     * @return TInterfaceType
     * @throws \ReflectionException If the interface name is not instantiable
     * @throws \InvalidArgumentException If the Reflection class or the name of the type is not an interface.
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function instantiate(
        string|ReflectionClass $interfaceNameOrReflection,
        MethodCallHandler $methodCallHandler
    ): object;
}