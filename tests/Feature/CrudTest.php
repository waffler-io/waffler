<?php

namespace Waffler\Tests\Feature;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Waffler\Client\Factory;
use Waffler\Tests\Feature\TestCaseTools\CrudTestCaseClient;

/**
 * Class CrudTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class CrudTest extends TestCase
{
    private CrudTestCaseClient $testCaseClient;

    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testCaseClient = Factory::make(CrudTestCaseClient::class, [
            'handler' => $this->mockHandler = new MockHandler()
        ]);
    }

    public function testGetAll(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('GET', $request->getMethod());
            self::assertEquals('foo', (string)$request->getUri()->getPath());
            self::assertEquals(['bar', 'baz'], $request->getHeader('X-Foo'));
            self::assertEquals(['application/json'], $request->getHeader('Accept'));
            self::assertEquals('foo=bar&baz=foo', $request->getUri()->getQuery());

            return new Response(body: '{"foo":"bar"}');
        });

        $response = $this->testCaseClient->getAll(['foo' => 'bar', 'baz' => 'foo']);

        self::assertEquals('bar', $response['foo']);
    }

    public function testGetById(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('GET', $request->getMethod());
            self::assertEquals('foo/1', (string)$request->getUri());
            self::assertEquals(['application/json'], $request->getHeader('Accept'));

            return new Response(body: '{"foo":"bar"}');
        });

        $response = $this->testCaseClient->getById(1);

        self::assertIsArray($response);
        self::assertEquals('bar', $response['foo']);
    }

    public function testCreate(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('POST', $request->getMethod());
            self::assertEquals('foo', (string)$request->getUri());
            self::assertEquals('{"foo":"bar"}', $request->getBody()->getContents());
            self::assertEquals(['application/json'], $request->getHeader('Content-Type'));
            self::assertEquals(['application/json'], $request->getHeader('Accept'));

            return new Response(body: '{"foo":"bar","id":1}');
        });

        $response = $this->testCaseClient->create(['foo' => 'bar']);

        self::assertIsArray($response);
        self::assertArrayHasKey('foo', $response);
        self::assertArrayHasKey('id', $response);
        self::assertEquals('bar', $response['foo']);
        self::assertEquals(1, $response['id']);
    }

    public function testUpdate(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('PATCH', $request->getMethod());
            self::assertEquals('foo/1', (string)$request->getUri());
            self::assertEquals('{"foo":"baz"}', $request->getBody()->getContents());
            self::assertEquals(['application/json'], $request->getHeader('Content-Type'));
            self::assertEquals(['application/json'], $request->getHeader('Accept'));

            return new Response(body: '{"foo":"baz"}');
        });

        $response = $this->testCaseClient->update(1, ['foo' => 'baz']);

        self::assertIsArray($response);
        self::assertArrayHasKey('foo', $response);
        self::assertEquals('baz', $response['foo']);
    }

    public function testReplace(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('PUT', $request->getMethod());
            self::assertEquals('foo/1', (string)$request->getUri());
            self::assertEquals('{"foo":"baz"}', $request->getBody()->getContents());
            self::assertEquals(['application/json'], $request->getHeader('Content-Type'));
            self::assertEquals(['application/json'], $request->getHeader('Accept'));

            return new Response(body: '{"foo":"baz"}');
        });

        $response = $this->testCaseClient->replace(1, ['foo' => 'baz']);

        self::assertIsArray($response);
        self::assertArrayHasKey('foo', $response);
        self::assertEquals('baz', $response['foo']);
    }

    public function testDelete(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('DELETE', $request->getMethod());
            self::assertEquals('foo/1', (string)$request->getUri());
            self::assertEquals(['application/json'], $request->getHeader('Accept'));

            return new Response(body: '{"message":"deleted"}');
        });

        $response = $this->testCaseClient->delete(1);
        self::assertIsArray($response);
        self::assertArrayHasKey('message', $response);
        self::assertEquals('deleted', $response['message']);
    }

    public function testPreflight(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('OPTIONS', $request->getMethod());
            self::assertEquals('foo', (string)$request->getUri());

            return new Response();
        });

        $response = $this->testCaseClient->preflight();
        self::assertEquals(200, $response);
    }

    public function testHead(): void
    {
        $this->mockHandler->append(function (RequestInterface $request): Response {
            self::assertEquals('HEAD', $request->getMethod());
            self::assertEquals('foo', (string)$request->getUri());

            return new Response();
        });

        $response = $this->testCaseClient->head();
        self::assertEquals(200, $response);
    }
}