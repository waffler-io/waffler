<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

require __DIR__ . '/../vendor/autoload.php';

use Waffler\Component\Attributes\Request\Json;
use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Attributes\Request\Query;
use Waffler\Component\Attributes\Verbs\Delete;
use Waffler\Component\Attributes\Verbs\Get;
use Waffler\Component\Attributes\Verbs\Patch;
use Waffler\Component\Attributes\Verbs\Post;
use Waffler\Component\Client\Factory;

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

$jsonplaceholder = Factory::default()
    ->make(JsonPlaceholderInterface::class, [
        'base_uri' => 'https://jsonplaceholder.typicode.com/'
    ]);

$users = $jsonplaceholder->getUsers();

var_dump($users);
