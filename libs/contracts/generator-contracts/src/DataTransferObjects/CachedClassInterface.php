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

namespace Waffler\Contracts\Generator\DataTransferObjects;

/**
 * Defines a contract for a cached class interface representation.
 *
 * This interface provides the structure for accessing
 * the fully qualified name of the interface, the fully qualified name of the class,
 * and the source code associated with the class.
 * @template I
 * @template C of I
 */
interface CachedClassInterface
{
    /**
     * @var class-string<I>
     */
    public string $interfaceFqn { get; }

    /**
     * @var class-string<C>
     */
    public string $classFqn { get; }
}
