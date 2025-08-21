<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Fixtures;

use Waffler\Component\Attributes\Auth\Basic;
use Waffler\Component\Attributes\Auth\Bearer;
use Waffler\Component\Attributes\Auth\Digest;
use Waffler\Component\Attributes\Auth\Ntml;
use Waffler\Component\Attributes\Request\Body;
use Waffler\Component\Attributes\Request\FormData;
use Waffler\Component\Attributes\Request\FormParam;
use Waffler\Component\Attributes\Request\Json;
use Waffler\Component\Attributes\Request\JsonParam;
use Waffler\Component\Attributes\Request\Multipart;
use Waffler\Component\Attributes\Request\Path;
use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Attributes\Request\Produces;
use Waffler\Component\Attributes\Request\Query;
use Waffler\Component\Attributes\Request\QueryParam;
use Waffler\Component\Attributes\Request\Timeout;
use Waffler\Component\Attributes\Utils\NestedResource;
use Waffler\Component\Attributes\Utils\RawOptions;
use Waffler\Component\Attributes\Utils\Suppress;
use Waffler\Component\Attributes\Utils\Unwrap;
use Waffler\Component\Attributes\Verbs\Delete;
use Waffler\Component\Attributes\Verbs\Get;
use Waffler\Component\Attributes\Verbs\Head;
use Waffler\Component\Attributes\Verbs\Options;
use Waffler\Component\Attributes\Verbs\Patch;
use Waffler\Component\Attributes\Verbs\Post;
use Waffler\Component\Attributes\Verbs\Put;

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
    public function testQueryArray(#[QueryParam('search')] array $search): void;

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

    #[Get('foo/{foo}')]
    public function testOptionalPathParam(#[PathParam] ?int $foo): void;

    // Attribute: Request/FormData and Request/FormParam

    #[Get]
    public function testFormData(#[FormData] array $formData): void;

    #[Get]
    public function testFormParam(#[FormParam('foo')] string $foo): void;

    #[Get]
    public function testFormParam2(#[FormParam('foo')] ?string $foo = null): void;

    #[Get]
    public function testFormParam3(#[FormParam('foo')] mixed $foo = null): void;

    #[Get]
    public function testFormParamAndFormData(#[FormData] array $formData, #[FormParam('baz')] string $baz): void;

    #[Get]
    public function testFormParamAndFormData2(#[FormData] array $formData, #[FormParam('foo')] string $foo): void;

    // Attribute: Request/Multipart

    #[Get]
    public function testMultipart(#[Multipart] array $data): void;

    // Attribute: Request/Consumes and Request/Produces

    #[Get]
    #[Produces('application/json')]
    public function testProduces(): void;

    // Attribute: Request/Body

    #[Get]
    public function testBody(#[Body('text/plain')] string $body): void;

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

    // Attribute: NestedResource

    #[Path('foo/{fooId}')]
    #[NestedResource]
    public function nested(#[PathParam] int $fooId): NestedResourceClient;
}
