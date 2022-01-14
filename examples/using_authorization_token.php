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

interface ExampleClientInterface
{
    #[Get('users')]
    public function getUsers(#[Bearer] string $token): array;
}

$exampleClient = Factory::make(ExampleClientInterface::class, [
    'base_uri' => 'https://example.com/',
    'headers' => ['Authorization' => 'Bearer your-access-token-here'] //You can use here
]);

$users = $exampleClient->getUsers('your-access-token-here'); // Or pass via argument
