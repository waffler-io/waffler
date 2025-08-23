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

namespace Waffler\Component\Generator;

use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;

interface ClassNameGeneratorInterface
{
    /**
     * @param class-string<T> $interfaceFqn
     *
     * @return class-string<covariant T&WafflerImplConstructorInterface>
     * @template T of object
     */
    public function generateClassFqn(string $interfaceFqn): string;

    /**
     * @param class-string<T> $interfaceFqn
     *
     * @return non-empty-string
     * @template T of object
     */
    public function generateClassName(string $interfaceFqn): string;
}
