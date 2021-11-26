<?php

namespace Waffler\Tests\Tools\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface InterfaceWithValidMethodSignature
{
    public function test(string $value): string;

    public function validSignature2(
        int $first,
        string $second,
        array $third = [],
        ?RequestInterface $forth = null
    ): ResponseInterface;
}