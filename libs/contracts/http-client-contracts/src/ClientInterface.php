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

namespace Waffler\Contracts\HttpClient;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Interface ClientInterface.
 *
 * This interface is the base contract for all HTTP clients.
 * It is a subset of the Guzzle HTTP client interface. See {@see \GuzzleHttp\ClientInterface} for more information.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface ClientInterface
{
    /**
     * @param array $options See {@see RequestOptions} for all available options.
     */
    public function __construct(array $options = []);

    /**
     * @throws GuzzleException
     */
    public function request(string $method, UriInterface|string $uri, array $options = []): ResponseInterface;

    /**
     * @throws GuzzleException
     */
    public function requestAsync(string $method, UriInterface|string $uri, array $options = []): PromiseInterface;
}
