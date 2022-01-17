<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use Waffler\Client\MethodInvoker;
use Waffler\Client\ResponseParser;
use Waffler\Tests\Fixtures\Interfaces\MethodInvokerTestClientInterface;

/**
 * Class MethodInvokerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\MethodInvoker
 * @uses   \Waffler\Attributes\Verbs\AbstractHttpMethod
 * @uses   \Waffler\Attributes\Verbs\Get
 * @uses   \Waffler\Client\Readers\MethodReader
 * @uses   \Waffler\Client\Readers\ParameterReader
 * @uses   \Waffler\Client\Traits\InteractsWithAttributes
 * @uses   \Waffler\Attributes\Utils\Unwrap
 * @uses   \Waffler\Client\ResponseParser
 * @uses   \Waffler\arrayGet()
 */
class MethodInvokerTest extends TestCase
{
    private MockHandler $handler;

    private MethodInvoker $methodInvoker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->methodInvoker = new MethodInvoker(
            new ResponseParser(),
            new Client(['handler' => $this->handler = new MockHandler()])
        );
    }

    public function testMustNotReturnPromiseIfTheMethodIsNotAsynchronous(): void
    {
        $this->handler->append(fn () => new Response());
        $response = $this->methodInvoker->invokeMethod(
            new ReflectionMethod(
                MethodInvokerTestClientInterface::class,
                'syncMethod'
            ),
            []
        );
        self::assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testMustReturnPromiseIfTheMethodIsAsynchronous(): void
    {
        $this->handler->append(fn () => new Response());
        $response = $this->methodInvoker->invokeMethod(
            new ReflectionMethod(
                MethodInvokerTestClientInterface::class,
                'asyncMethod'
            ),
            []
        );
        self::assertInstanceOf(PromiseInterface::class, $response);
    }

    public function testMustReturnUnwrappedArrayData(): void
    {
        $this->handler->append(fn () => new Response(
            body: (string) json_encode(['wrapped' => ['data' => [123]]])
        ));
        $response = $this->methodInvoker->invokeMethod(
            new ReflectionMethod(
                MethodInvokerTestClientInterface::class,
                'methodWithWrapper'
            ),
            []
        );
        $this->assertEquals([123], $response);
    }
}
