<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client\Contracts;

/**
 * Interface FactoryInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface FactoryInterface
{
    /**
     * Factory method to create the client implementation.
     *
     * @param class-string<TInterface> $interfaceName Fully qualified name of the client interface.
     * @param array<string, mixed>     $options       An array of GuzzleHttp client options.
     *
     * @return object&TInterface
     * @throws \ReflectionException If the $interfaceName can't be reflected.
     * @throws \InvalidArgumentException If the $interfaceName is not a fully qualified name of an interface.
     * @template TInterface of object
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public static function make(string $interfaceName, array $options = []): object;
}
