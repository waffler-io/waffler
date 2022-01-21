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

use GuzzleHttp\Promise\PromiseInterface;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Request\Timeout;
use Waffler\Attributes\Utils\Suppress;
use Waffler\Attributes\Utils\Unwrap;
use Waffler\Attributes\Verbs\Get;

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
}
