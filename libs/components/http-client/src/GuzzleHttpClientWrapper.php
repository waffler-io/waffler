<?php

declare(strict_types=1);

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Waffler\Contracts\HttpClient\ClientInterface;

final readonly class GuzzleHttpClientWrapper implements ClientInterface
{
    private Client $client;

    public function __construct(array $options = [])
    {
        $this->client = new Client($options);
    }

    public function request(string $method, UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    public function requestAsync(string $method, UriInterface|string $uri, array $options = []): PromiseInterface
    {
        return $this->client->requestAsync($method, $uri, $options);
    }
}
