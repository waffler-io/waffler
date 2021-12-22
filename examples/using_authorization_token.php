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

use Waffler\Attributes\Auth\Bearer;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Verbs\Get;
use Waffler\Client\Factory;

/**
 * Interface JsonPlaceholderInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface ExampleClientInterface
{
    /**
     * Using authorization token as method argument.
     *
     * @param string $token
     *
     * @return array
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Get('users')]
    public function getUsers(#[Bearer] string $token): array;

    /**
     * Using authorization token as header mock.
     *
     * @param int $id
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Headers([
        'Authorization' => "Bearer <your-access-token-here>"
    ])]
    #[Get('users/{id}')]
    public function getUserById(#[PathParam] int $id);
}

$exampleClient = Factory::make(ExampleClientInterface::class, [
    'base_uri' => 'https://example.com/'
]);

$users = $exampleClient->getUsers('<your-access-token-here>');
