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
     * @param class-string<TInterfaceType>                   $interfaceName
     * @param \Waffler\Generator\Contracts\MethodCallHandler $methodCallHandler
     *
     * @return TInterfaceType
     * @throws \ReflectionException If the interface name is not instantiable
     * @throws \InvalidArgumentException If the Reflection class or the name of the type is not an interface.
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function instantiate(
        string $interfaceName,
        MethodCallHandler $methodCallHandler
    ): object;

    /**
     * Instantiate an interface by its name.
     *
     * @param \ReflectionClass<TInterfaceType>               $reflectionInterface
     * @param \Waffler\Generator\Contracts\MethodCallHandler $methodCallHandler
     *
     * @return TInterfaceType
     * @throws \InvalidArgumentException If the Reflection class or the name of the type is not an interface.
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function instantiateFromReflection(
        ReflectionClass $reflectionInterface,
        MethodCallHandler $methodCallHandler
    ): object;
}