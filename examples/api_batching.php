<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

require __DIR__.'/../vendor/autoload.php';

use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Verbs\Post;
use Waffler\Waffler\Client\Factory;

interface JsonPlaceholderInterface
{
    /**
     * Creates a new user.
     *
     * @param string $name
     * @param string $email
     * @param string $nickname
     *
     * @return array<string, string>
     */
    #[Post('users')]
    public function createUser(
        #[JsonParam('name')] string $name,
        #[JsonParam('email')] string $email,
        #[JsonParam('nickname')] string $nickname,
    ): array;

    /**
     * Creates multiple users.
     *
     * @param array<array<string>> $data
     *
     * @return array<array<string, string>>
     */
    #[Batch('createUser')]
    public function createManyUsers(array $data): array;
}

// not supported yet.
$client = (new Factory())->make(JsonPlaceholderInterface::class, [
    'base_uri' => 'https://jsonplaceholder.typicode.com/',
]);

// Send multiple requests at the same time
$response = $client->createManyUsers([
    ['foo', 'foo@example.com', 'foo'], // name, email, nickname...
    ['bar', 'bar@example.com', 'bar'],
    ['baz', 'baz@example.com', 'baz'],
]);

var_dump($response);
