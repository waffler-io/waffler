<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

require __DIR__ . '/../vendor/autoload.php';

use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Verbs\Delete;
use Waffler\Waffler\Attributes\Verbs\Get;
use Waffler\Waffler\Attributes\Verbs\Patch;
use Waffler\Waffler\Attributes\Verbs\Post;
use Waffler\Waffler\Client\Factory;

interface JsonPlaceholderInterface
{
    #[Get('users')]
    public function getUsers(#[Query] array $filters = []): array;

    #[Get('users/{id}')]
    public function getUserById(#[PathParam('id')] int $id): array;

    #[Post('users')]
    public function createUser(#[Json] array $user): array;

    #[Patch('users/{id}')]
    public function updateUser(#[PathParam('id')] int $id, #[Json] array $data): array;

    #[Delete('users/{id}')]
    public function deleteUser(#[PathParam('id')] int $id): void;
}

$jsonplaceholder = Factory::make(JsonPlaceholderInterface::class, [
    'base_uri' => 'https://jsonplaceholder.typicode.com/'
]);

$users = $jsonplaceholder->getUsers();
