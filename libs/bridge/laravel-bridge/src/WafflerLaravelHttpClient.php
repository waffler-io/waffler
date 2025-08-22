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

namespace Waffler\Bridge\Laravel;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Throwable;
use Waffler\Contracts\HttpClient\ClientInterface;

final readonly class WafflerLaravelHttpClient implements ClientInterface
{
    public function __construct(private array $options = []) {}

    /**
     * @throws Throwable
     */
    public function request(string $method, UriInterface|string $uri, array $options = []): ResponseInterface
    {
        try {
            return Http::withOptions($this->options)
                ->send($method, (string) $uri, $options)
                ->toPsrResponse();
        } catch (HttpClientException $e) {
            throw $this->handleLaravelThrowable($e);
        }
    }

    private function handleLaravelThrowable(Throwable $e): Throwable
    {
        if ($e instanceof HttpClientException) {
            return $e->getPrevious() ?? $e;
        }
        return $e;
    }

    public function requestAsync(string $method, UriInterface|string $uri, array $options = []): PromiseInterface
    {
        // @phpstan-ignore-next-line
        return Http::withOptions($this->options)
            ->async()
            ->send($method, (string) $uri, $options)
            ->then(
                static fn(Response $response) => $response->toPsrResponse(),
                fn(Throwable $e) => $this->handleLaravelThrowable($e),
            );
    }
}
