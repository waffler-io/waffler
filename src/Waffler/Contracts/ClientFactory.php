<?php

namespace Waffler\Contracts;

/**
 * Interface ClientFactory.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface ClientFactory
{
    /**
     * @param class-string<TInterfaceName> $interface
     * @param array                        $options
     *
     * @return TInterfaceName
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TInterfaceName of object
     */
    public static function implements(string $interface, array $options = []): mixed;
}