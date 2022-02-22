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
use Waffler\Waffler\Attributes\Request\Headers;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Request\Produces;
use Waffler\Waffler\Attributes\Request\Timeout;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Utils\Suppress;
use Waffler\Waffler\Attributes\Utils\Unwrap;
use Waffler\Waffler\Attributes\Verbs\Get;

/**
 * Interface MethodReaderTestCaseClient.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface MethodReaderTestCaseClient
{
    #[Suppress]
    public function suppressed(): void;

    public function unsuppressed(): void;

    public function doNotWrap(): void;

    #[Unwrap]
    public function wrap(): void;

    public function async(): PromiseInterface;

    #[Get]
    public function verbGet(): void;

    public function doesNotHaveVerb(): void;

    public function returnType(): string;

    public function mixedReturnType();

    public function unionType(): string|int;

    #[Get('/foo/{a}/bar/{b}')]
    public function testPath(#[PathParam] int $a, #[PathParam] int $b): void;

    public function noRawOptions(): void;

    #[Produces('application/json')]
    #[Timeout(100)]
    #[Suppress]
    #[Headers(['foo' => ['bar']])]
    public function withManyOptions(): void;

    #[Batch('testPath')]
    public function batchedMethod(array $args): array;
}
