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

use Waffler\Waffler\Attributes\Auth\Basic;
use Waffler\Waffler\Attributes\Auth\Bearer;
use Waffler\Waffler\Attributes\Auth\Digest;
use Waffler\Waffler\Attributes\Auth\Ntml;
use Waffler\Waffler\Attributes\Request\Body;
use Waffler\Waffler\Attributes\Request\FormData;
use Waffler\Waffler\Attributes\Request\FormParam;
use Waffler\Waffler\Attributes\Request\HeaderParam;
use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Request\Multipart;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Request\QueryParam;
use Waffler\Waffler\Attributes\Utils\RawOptions;

/**
 * Interface ParameterReaderTestClientInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface ParameterReaderTestClientInterface
{
    public function queryAndQueryParamTest(#[Query] array $foo, #[QueryParam('bar')] string $bar): void;

    public function formDataAndFormParam(#[FormData] array $foo, #[FormParam('bar')] string $bar): void;

    public function headerParamWithBearerAndBody(#[HeaderParam('foo')] string $foo, #[Bearer] string $bar, #[Body('baz')] string $baz = 'baz'): void;

    public function multipart(#[Multipart] array $multipart): void;

    public function multipleMultipart(#[Multipart] array $multipart1, #[Multipart] array $multipart2): void;

    public function basicAuth(#[Basic] array $auth): void;

    public function digestAuth(#[Digest] array $auth): void;

    public function ntmlAuth(#[Ntml] array $auth): void;

    public function body(#[Body] string $body): void;

    public function rawOptions(#[RawOptions] array $options): void;

    public function jsonAndJsonParam(#[Json] array $json, #[JsonParam('bar')] string $bar): void;

    public function parsePath(#[PathParam('id')] string $id, #[PathParam('name')] string $name): void;

    public function missingArgument(#[Body] string $foo): void;
}
