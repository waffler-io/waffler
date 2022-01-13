<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Fixtures\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface InterfaceWithValidMethodSignature
{
    public function test(string $value): string;

    /**
     * @param int                                     $first
     * @param string                                  $second
     * @param array<T>                            $third
     * @param \Psr\Http\Message\RequestInterface|null $forth
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @phpstan-template T
     */
    public function validSignature2(
        int $first,
        string $second,
        array $third = [],
        ?RequestInterface $forth = null
    ): ResponseInterface;
}
