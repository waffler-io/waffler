<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Tools\Interfaces;

use Waffler\Attributes\Auth\Basic;
use Waffler\Attributes\Auth\Bearer;
use Waffler\Attributes\Auth\Digest;
use Waffler\Attributes\Auth\Ntml;
use Waffler\Attributes\Request\Body;
use Waffler\Attributes\Request\Consumes;
use Waffler\Attributes\Request\FormData;
use Waffler\Attributes\Request\FormParam;
use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\JsonParam;
use Waffler\Attributes\Request\Multipart;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Request\QueryParam;
use Waffler\Attributes\Request\Timeout;
use Waffler\Attributes\Utils\RawOptions;
use Waffler\Attributes\Utils\Suppress;
use Waffler\Attributes\Utils\Unwrap;
use Waffler\Attributes\Verbs\Delete;
use Waffler\Attributes\Verbs\Get;
use Waffler\Attributes\Verbs\Head;
use Waffler\Attributes\Verbs\Options;
use Waffler\Attributes\Verbs\Patch;
use Waffler\Attributes\Verbs\Post;
use Waffler\Attributes\Verbs\Put;

/**
 * Interface FeatureTestCaseClient.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[Path('/api/v1')]
interface FeatureTestCaseClient
{
    // Attribute: Request/Timeout
    #[Get]
    #[Timeout(100)]
    public function testTimeout(): void;

    // Attribute: Request/Json and Request/JsonParam

    #[Get]
    public function testJson(#[Json] array $json): void;

    #[Get]
    public function testJsonParam(#[JsonParam('foo')] string $foo, #[JsonParam('bar.baz')] string $gim): void;

    #[Get]
    public function testJsonAndJsonParam(#[Json] array $json, #[JsonParam('baz')] string $gim);

    #[Get]
    public function testJsonAndJsonParam2(#[Json] array $json, #[JsonParam('foo')] string $foo);

    // Attribute: Request/Query and Request/QueryParam

    #[Get]
    public function testQuery(#[Query] array $query): void;

    #[Get]
    public function testQueryParam(#[QueryParam('foo')] string $foo): void;

    #[Get]
    public function testQueryAndQueryParam(#[Query] array $query, #[QueryParam('baz')] string $gim): void;

    #[Get]
    public function testQueryAndQueryParam2(#[Query] array $query, #[QueryParam('foo')] string $foo): void;

    // Attribute: Request/Path and Request/PathParam

    #[Get]
    #[Path('foo/bar/baz')]
    public function testPath(): void;

    #[Get]
    #[Path('foo/{foo}/bar/{abc}')]
    public function testPathAndPathParam(#[PathParam] int $foo, #[PathParam('abc')] int $bar): void;

    #[Get('baz/{baz}')]
    #[Path('foo/{foo}/bar/{bar}')]
    public function testPathAndPathParam2(#[PathParam] int $foo, #[PathParam] int $bar, #[PathParam] int $baz): void;

    // Attribute: Request/FormData and Request/FormParam

    #[Get]
    public function testFormData(#[FormData] array $formData): void;

    #[Get]
    public function testFormParam(#[FormParam('foo')] string $foo): void;

    #[Get]
    public function testFormParamAndFormData(#[FormData] array $formData, #[FormParam('baz')] string $baz): void;

    #[Get]
    public function testFormParamAndFormData2(#[FormData] array $formData, #[FormParam('foo')] string $foo): void;

    // Attribute: Request/Multipart

    #[Get]
    public function testMultipart(#[Multipart] array $data): void;

    // Attribute: Request/Consumes and Request/Produces

    #[Get]
    #[Consumes('application/json')]
    public function testConsumes(): void;

    #[Get]
    #[Produces('application/json')]
    public function testProduces(): void;

    // Attribute: Request/Body

    #[Get]
    public function testBody(#[Body] string $body): void;

    // Attribute: Auth/Basic

    #[Get]
    public function testBasic(#[Basic] array $loginAndPassword): void;

    // Attribute: Auth/Bearer

    #[Get]
    public function testBearer(#[Bearer] string $token): void;

    // Attribute: Auth/Digest

    #[Get]
    public function testDigest(#[Digest] array $userNamePasswordAndDigest): void;

    // Attribute: Auth/Ntml

    #[Get]
    public function testNtml(#[Ntml] array $userNamePasswordAndNtml): void;

    // Attribute: Verbs/Delete, Verbs/Get, Verbs/Head, Verbs/Options, Verbs/Patch, Verbs/Post and Verbs/Put

    #[Delete('delete')]
    public function testDelete(): void;

    #[Get('get')]
    public function testGet(): void;

    #[Head('head')]
    public function testHead(): void;

    #[Options('options')]
    public function testOptions(): void;

    #[Patch('patch')]
    public function testPatch(): void;

    #[Post('post')]
    public function testPost(): void;

    #[Put('put')]
    public function testPut(): void;

    // Attribute: Utils/RawOptions

    #[Get]
    public function testRawOptions(#[RawOptions] array $options): void;

    #[Get]
    #[Suppress]
    public function testSuppress(): int;

    #[Get]
    #[Unwrap('data.contents')]
    public function testUnwrap(): array;
}
