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

namespace Waffler\Contracts\Generator;

use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

/**
 * Interface ClientInterface.
 *
 * This interface sets the contract for a class generator.
 * A class generator is responsible for generating the implementation of a client
 * The generator must receive the interface FQN and return the source code of the implementation.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface ClassGeneratorInterface
{
    /**
     * Generates the implementation of the interface.
     *
     * @param class-string<T> $interfaceFqn The interface FQN.
     *
     * @return string The source code of the implementation.
     * @throws GeneratorExceptionInterface
     *
     * @template T of object
     */
    public function generateClass(string $interfaceFqn): string;
}
