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
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Utils\Batch;
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

    #[Get('{bar}/{baz}')]
    public function methodToBeBatchedSyncReturnType(#[PathParam] string $bar, #[PathParam] string $baz): string;

    #[Get('{bar}/{baz}')]
    public function methodToBeBatchedAsyncReturnType(#[PathParam] string $bar, #[PathParam] string $baz): PromiseInterface;

    #[Batch('methodToBeBatchedSyncReturnType')]
    public function batchMethodWithInvalidSingleArgument(int $args): array;

    #[Batch('methodToBeBatchedSyncReturnType')]
    public function batchMethodWithInvalidMultipleArguments(array $arg1, array $arg2): array;

    #[Batch('methodToBeBatchedSyncReturnType')]
    public function batchMethodWithInvalidReturnType(array $args): void;

    #[Batch('methodToBeBatchedSyncReturnType')]
    public function batchMethodSync(array $args): array;

    #[Batch('methodToBeBatchedSyncReturnType')]
    public function batchMethodAsync(array $args): PromiseInterface;

    #[Batch('methodToBeBatchedAsyncReturnType')]
    public function batchMethodAndReturnResponseInstance(array $args): PromiseInterface;

    #[Batch('batchMethodAndReturnResponseInstance')]
    public function batchABatch(array $args): PromiseInterface;
}
