<?php

namespace Waffler\Tests\Feature\TestCaseTools;

use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Verbs\Delete;
use Waffler\Attributes\Verbs\Get;
use Waffler\Attributes\Verbs\Head;
use Waffler\Attributes\Verbs\Patch;
use Waffler\Attributes\Verbs\Post;
use Waffler\Attributes\Verbs\Put;

interface CrudTestCaseClient
{
    /**
     * @param array<string, int|string> $query
     *
     * @return array<array<string, string>>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Get('/foo')]
    #[Headers(['X-Foo' => ['bar', 'baz']])]
    #[Produces('application/json')]
    public function getAll(#[Query] array $query = []): array;

    /**
     * @param int $id
     *
     * @return array<string,string>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Get('/foo/{id}')]
    #[Produces('application/json')]
    public function getById(#[PathParam] int $id): array;

    /**
     * @param array<string,string> $data
     *
     * @return array<string,string>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Post('/foo')]
    #[Produces('application/json')]
    public function create(#[Json] array $data): array;

    /**
     * @param int                  $fooId
     * @param array<string,string> $data
     *
     * @return array<string,string>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Patch('/foo/{fooId}')]
    #[Produces('application/json')]
    public function update(#[PathParam] int $fooId, #[Json] array $data): array;

    /**
     * @param int                   $fooId
     * @param array<string, string> $data
     *
     * @return array<string, string>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Put('/foo/{fooId}')]
    #[Produces('application/json')]
    public function replace(#[PathParam] int $fooId, #[Json] array $data): array;

    /**
     * @param int $id
     *
     * @return array<string, string>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Delete('/foo/{fooId}')]
    #[Produces('application/json')]
    public function delete(#[PathParam('fooId')] int $id): array;

    /**
     * @return int
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Head('/foo')]
    public function preflight(): int;
}