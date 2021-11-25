<?php

namespace Waffler\Client\Contracts;

use ReflectionClass;

/**
 * Interface Factory.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template TClientInterface of object
 */
interface Factory
{
    /**
     * Builds a concrete object from the client interface.
     *
     * @param class-string<TClientInterface> $interfaceName The fully qualified name of the client interface.
     * @param array<string,mixed>            $guzzleOptions Guzzle http options.
     *
     * @return TClientInterface
     * @throws \ReflectionException If the type can not be reflected.
     * @throws \InvalidArgumentException If the type is not an interface.
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function build(string $interfaceName, array $guzzleOptions = []): object;

    /**
     * Builds a concrete object from the client interface.
     *
     * @param \ReflectionClass<TClientInterface> $reflectionInterface The reflection of the client interface.
     * @param array<string,mixed>                $guzzleOptions       Guzzle http options.
     *
     * @return TClientInterface
     * @throws \InvalidArgumentException If the type is not an interface.
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function buildFromReflection(ReflectionClass $reflectionInterface, array $guzzleOptions = []): object;
}