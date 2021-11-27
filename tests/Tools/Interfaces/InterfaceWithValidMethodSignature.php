<?php

namespace Waffler\Tests\Tools\Interfaces;

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