<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Utils;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\CleanStart;
use Waffler\Component\Client\Tests\TestCase;
use Psr\Http\Message\RequestInterface;
use Waffler\Component\Attributes\Request\Path;
use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Attributes\Request\Query;
use Waffler\Component\Attributes\Utils\NestedResource;
use Waffler\Component\Attributes\Verbs\Get;

/**
 * Class GenericNestedResourceTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
class GenericNestedResourceTest extends TestCase
{
    use CleanStart;

    public function testItMustInheritsThePathFromTheParentFactoryMethod(): void
    {
        $handler = new MockHandler();
        $handler->append(function (RequestInterface $request) {
            $this->assertEquals('https://jsonplaceholder.typicode.com/users/1/posts/', (string)$request->getUri());
            return new Response(body: '{}');
        });
        $client = $this->factory->make(JsonPlaceholderApi::class, [
            'base_uri' => 'https://jsonplaceholder.typicode.com/',
            'handler' => $handler,
        ]);
        $client->resource('users')
            ->relation('posts', '1')
            ->all();
    }
}

interface ResourceApi
{
    #[Get]
    public function all(#[Query] array $filter = []): array;

    #[NestedResource]
    #[Path('{parentId}/{name}')]
    public function relation(#[PathParam] string $name, #[PathParam] string $parentId = ''): ResourceApi;
}

interface JsonPlaceholderApi
{
    #[Get]
    public function isOnline(): bool;

    #[NestedResource]
    #[Path('{name}')]
    public function resource(#[PathParam] string $name): ResourceApi;
}
