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

use Waffler\Contracts\Generator\DataTransferObjects\CachedClassInterface;
use Waffler\Contracts\Generator\Exceptions\ClassNotFoundExceptionInterface;

/**
 * Interface for managing the interaction with class repositories.
 */
interface ClassRepositoryInterface
{
    /**
     * Saves the provided source associated with the specified interface fully qualified name.
     *
     * @param class-string<T>  $interfaceFqn The fully qualified name of the interface.
     * @param non-empty-string $source       The source code to be saved.
     *
     * @return CachedClassInterface<T, covariant T>
     * @template T of object
     */
    public function save(string $interfaceFqn, string $source): CachedClassInterface;

    /**
     * Checks if the implementation class for the given interface fully qualified name exists.
     *
     * @param class-string<T> $interfaceFqn The fully qualified name of the interface.
     *
     * @return bool True if the implementation class exists, false otherwise.
     * @template T of object
     */
    public function has(string $interfaceFqn): bool;

    /**
     * Retrieves the implementation class name for the given interface fully qualified name.
     *
     * @param class-string<T> $interfaceFqn The fully qualified name of the interface.
     *
     * @return CachedClassInterface<T, covariant T>
     *
     * @throws ClassNotFoundExceptionInterface If the implementation class for the given interface is not found.
     * @template T of object
     */
    public function get(string $interfaceFqn): CachedClassInterface;
}
