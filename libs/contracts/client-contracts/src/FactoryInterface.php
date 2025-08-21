<?php

declare(strict_types=1);

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Contracts\Client;

use InvalidArgumentException;
use ReflectionException;

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
     * @param class-string<T> $interface Fully qualified name of the client interface.
     * @param array<string, mixed>     $options   An array of GuzzleHttp client options.
     *
     * @return object&T
     * @throws ReflectionException If the $interfaceName can't be reflected.
     * @throws InvalidArgumentException If the $interfaceName is not a fully qualified name of an interface.
     * @template T of object
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function make(string $interface, array $options = []): object;
}
