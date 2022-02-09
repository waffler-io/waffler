<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use Waffler\Waffler\Client\MethodInvoker;
use Waffler\Waffler\Client\ResponseParser;
use Waffler\Waffler\Tests\Fixtures\Interfaces\MethodInvokerTestClientInterface;

/**
 * Class MethodInvokerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\MethodInvoker
 * @uses   \Waffler\Waffler\Attributes\Verbs\AbstractHttpMethod
 * @uses   \Waffler\Waffler\Attributes\Verbs\Get
 * @uses   \Waffler\Waffler\Client\Readers\MethodReader
 * @uses   \Waffler\Waffler\Client\Readers\ParameterReader
 * @uses   \Waffler\Waffler\Client\Traits\InteractsWithAttributes
 * @uses   \Waffler\Waffler\Attributes\Utils\Unwrap
 * @uses   \Waffler\Waffler\Client\ResponseParser
 * @uses   \Waffler\Waffler\arrayGet()
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
