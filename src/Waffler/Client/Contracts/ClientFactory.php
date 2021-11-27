<?php

namespace Waffler\Client\Contracts;

/**
 * Interface ClientFactory.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-template TInterfaceType of object
 */
interface ClientFactory
{
    /**
     * Generates the implementation for the given client interface.
     *
     * @param class-string<TInterfaceType> $interfaceName Fully qualified name of the client interface.
     * @param array<string, mixed>         $options       GuzzleHttp Client options.
     *
     * @return TInterfaceType
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function makeImplementation(): mixed;
}