<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

require __DIR__ . '/../vendor/autoload.php';

use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Verbs\Delete;
use Waffler\Attributes\Verbs\Get;
use Waffler\Attributes\Verbs\Patch;
use Waffler\Attributes\Verbs\Post;
use Waffler\Client\Factory;

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
