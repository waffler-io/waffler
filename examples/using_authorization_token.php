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

use Waffler\Component\Attributes\Auth\Bearer;
use Waffler\Component\Attributes\Verbs\Get;
use Waffler\Component\Client\Factory;

interface ExampleClientInterface
{
    #[Get('users')]
    public function getUsers(#[Bearer] string $token): array;
}

$exampleClient = Factory::default()
    ->make(ExampleClientInterface::class, [
        'base_uri' => 'https://jsonplaceholder.typicode.com/',
        'headers' => ['Authorization' => 'Bearer your-access-token-here'] //You can use here
    ]);

$users = $exampleClient->getUsers('your-access-token-here'); // Or pass via argument
