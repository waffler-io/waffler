<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Generator\Contracts;

/**
 * Interface InterfaceInstantiator.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface InterfaceInstantiator
{
    /**
     * Instantiate an interface by its name.
     *
     * @param \Waffler\Waffler\Generator\Contracts\MethodCallHandler<TInterfaceType> $methodCallHandler
     *
     * @return object&TInterfaceType
     * @throws \ReflectionException If the interface name is not instantiable
     * @throws \InvalidArgumentException If the Reflection class or the name of the type is not an interface.
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TInterfaceType of object
     */
    public function instantiate(MethodCallHandler $methodCallHandler): object;
}
