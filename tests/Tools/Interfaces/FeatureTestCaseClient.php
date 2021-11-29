<?php

namespace Waffler\Tests\Tools\Interfaces;

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
use Waffler\Attributes\Verbs\Get;

/**
 * Interface FeatureTestCaseClient.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[Path('/api/v1')]
interface FeatureTestCaseClient
{
    // Attribute: Timeout
    #[Get]
    #[Timeout(100)]
    public function testTimeout(): void;

    // Attribute: Json and JsonParam

    #[Get]
    public function testJson(#[Json] array $json): void;

    #[Get]
    public function testJsonParam(#[JsonParam('foo')] string $foo): void;

    #[Get]
    public function testJsonAndJsonParam(#[Json] array $json, #[JsonParam('baz')] string $gim);

    #[Get]
    public function testJsonAndJsonParam2(#[Json] array $json, #[JsonParam('foo')] string $foo);

    // Attribute: Query and QueryParam

    #[Get]
    public function testQuery(#[Query] array $query): void;

    #[Get]
    public function testQueryParam(#[QueryParam('foo')] string $foo): void;

    #[Get]
    public function testQueryAndQueryParam(#[Query] array $query, #[QueryParam('baz')] string $gim): void;

    #[Get]
    public function testQueryAndQueryParam2(#[Query] array $query, #[QueryParam('foo')] string $foo): void;

    // Attribute: Path and PathParam

    #[Get]
    #[Path('foo/bar/baz')]
    public function testPath(): void;

    #[Get]
    #[Path('foo/{foo}/bar/{abc}')]
    public function testPathAndPathParam(#[PathParam] int $foo, #[PathParam('abc')] int $bar): void;

    #[Get('baz/{baz}')]
    #[Path('foo/{foo}/bar/{bar}')]
    public function testPathAndPathParam2(#[PathParam] int $foo, #[PathParam] int $bar, #[PathParam] int $baz): void;

    // Attribute: FormData and FormParam

    #[Get]
    public function testFormData(#[FormData] array $formData): void;

    #[Get]
    public function testFormParam(#[FormParam('foo')] string $foo): void;

    #[Get]
    public function testFormParamAndFormData(#[FormData] array $formData, #[FormParam('baz')] string $baz): void;

    #[Get]
    public function testFormParamAndFormData2(#[FormData] array $formData, #[FormParam('foo')] string $foo): void;

    // Attribute: Multipart

    #[Get]
    public function testMultipart(#[Multipart] array $data): void;

    // Attribute: Consumes and Produces

    #[Get]
    #[Consumes('application/json')]
    public function testConsumes(): void;

    #[Get]
    #[Produces('application/json')]
    public function testProduces(): void;

    // Attribute: Body

    #[Get]
    public function testBody(#[Body] string $body): void;
}