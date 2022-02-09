<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Fixtures\Interfaces;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Waffler\Waffler\Attributes\Utils\Unwrap;
use Waffler\Waffler\Attributes\Verbs\Get;

/**
 * Interface MethodInvokerTestClientInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface MethodInvokerTestClientInterface
{
    #[Get]
    public function asyncMethod(): PromiseInterface;

    #[Get]
    public function syncMethod(): ResponseInterface;

    #[Get]
    #[Unwrap('wrapped.data')]
    public function methodWithWrapper(): array;
}
