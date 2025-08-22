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

use ReflectionException;
use Waffler\Contracts\Generator\Exceptions\ClassNotFoundExceptionInterface;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

/**
 * Interface PregeneratesClientsInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface PregeneratesClientsInterface
{
    /**
     * Generates and caches the implementation of the given interfaces to future uses.
     *
     * @param class-string<T> $interface Fully qualified name of the client interface.
     *
     * @return void
     * @throws ReflectionException If the $interfaceName can't be reflected.
     * @throws ClassNotFoundExceptionInterface
     * @throws GeneratorExceptionInterface
     * @template T of object
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function warmup(string $interface): void;
}
