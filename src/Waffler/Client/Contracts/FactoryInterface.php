<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client\Contracts;

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
     * @psalm-param class-string<TInterface> $interfaceName Fully qualified name of the client interface.
     * @psalm-param array<string, mixed>     $options       An array of guzzle http client options.
     *
     * @return TInterface
     * @throws \ReflectionException
     * @throws \Exception
     * @phpstan-template TInterface of object
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public static function make(string $interfaceName, array $options = []): object;
}
