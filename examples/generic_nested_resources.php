<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Utils\NestedResource;
use Waffler\Attributes\Verbs\Get;
use Waffler\Client\Factory;

require '../vendor/autoload.php';


interface ResourceApi
{
    #[Get]
    public function all(#[Query] array $filter = []): array;

    #[NestedResource]
    #[Path('{parentId}/{name}')]
    public function relation(#[PathParam] string $name, #[PathParam] $parentId = ''): ResourceApi;
}

interface JsonPlaceholderApi
{
    #[Get]
    public function isOnline(): bool;

    #[NestedResource]
    #[Path('{name}')]
    public function resource(#[PathParam] string $name): ResourceApi;
}

$api = Factory::make(JsonPlaceholderApi::class, [
    'base_uri' => 'https://jsonplaceholder.typicode.com/'
]);

$posts = $api->resource('users')
    ->relation('posts', 1)
    ->all(); // GET /users/1/posts

var_dump($posts);

$comments = $api->resource('posts')
    ->relation('comments', 1)
    ->all(); // GET /posts/1/comments

var_dump($comments);
