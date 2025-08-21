<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Factory;

use ReflectionException;
use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;

interface FactoryInterface
{
    /**
     * @param class-string<T> $interface
     *
     * @return class-string<T&WafflerImplConstructorInterface>
     * @throws ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T of object
     */
    public function generateForInterface(string $interface): string;
}
