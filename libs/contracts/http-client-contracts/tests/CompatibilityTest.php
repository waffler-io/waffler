<?php

declare(strict_types = 1);

namespace Waffler\Contracts\HttpClient\Tests;

use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\Attributes\Group;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Waffler\Contracts\HttpClient\ClientInterface;

#[Group('waffler/http-client-contracts')]
class CompatibilityTest extends TestCase
{
    public function testClientInterfaceCompatibility()
    {
        $client = new class implements ClientInterface {
            public function __construct(array $options = []) {}

            public function request(string $method, UriInterface|string $uri, array $options = []): ResponseInterface
            {
                return mock(ResponseInterface::class);
            }

            public function requestAsync(
                string $method,
                UriInterface|string $uri,
                array $options = [],
            ): PromiseInterface {
                return mock(PromiseInterface::class);
            }
        };
        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(ResponseInterface::class, $client->request('GET', 'http://localhost'));
        $this->assertInstanceOf(PromiseInterface::class, $client->requestAsync('GET', 'http://localhost'));
    }
}
