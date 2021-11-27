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
     * @return TInterfaceType
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function makeImplementation(): mixed;
}