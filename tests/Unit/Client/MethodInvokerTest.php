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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Waffler\Attributes\Contracts\Verb;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Request\Timeout;
use Waffler\Attributes\Utils\NestedResource;
use Waffler\Attributes\Utils\Suppress;
use Waffler\Attributes\Utils\Unwrap;
use Waffler\Attributes\Verbs\Get;
use Waffler\Client\MethodInvoker;
use Waffler\Client\ResponseParser;

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
 */
class MethodInvokerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ReflectionClass $declaringClass;

    private MethodInvoker $methodInvoker;

    private ResponseParser $responseParser;

    private ClientInterface $client;

    private ReflectionMethod $method;

    private Verb $verb;

    private PromiseInterface $promise;

    private ResponseInterface $responseInterface;

    protected function setUp(): void
    {
        parent::setUp();

        $this->methodInvoker = new MethodInvoker(
            $this->responseParser = m::mock(ResponseParser::class),
            $this->client = m::mock(ClientInterface::class)
        );

        $this->declaringClass = m::mock(ReflectionClass::class);
        $this->method = m::mock(ReflectionMethod::class);
        $this->verb = new Get();
        $this->promise = m::mock(PromiseInterface::class);
        $this->responseInterface = m::mock(ResponseInterface::class);
    }

    public function testMustNotReturnPromiseIfTheMethodIsNotAsynchronous(): void
    {
        $this->prepareBasicsInteractions(true);

        $this->method->shouldReceive('hasReturnType')
            ->atLeast()
            ->once()
            ->andReturn(false);

        $this->promise->shouldReceive('wait')
            ->once()
            ->andReturn($this->responseInterface);

        $this->method->shouldReceive('getAttributes')
            ->with(Unwrap::class)
            ->atLeast()->once()
            ->andReturn([$unwrap = m::mock(ReflectionAttribute::class)]);
        $unwrap->shouldReceive('newInstance')->atLeast()->once()->andReturn(new Unwrap());

        $this->responseParser->shouldReceive('parse')
            ->once()
            ->withAnyArgs()
            ->andReturn($this->responseInterface);

        $response = $this->methodInvoker->invokeMethod($this->method, []);

        self::assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testMustReturnPromiseIfTheMethodIsAsynchronous(): void
    {
        $this->prepareBasicsInteractions();

        $this->method->shouldReceive('hasReturnType')
            ->atLeast()
            ->once()
            ->andReturn(true);

        $reflectionReturnType = m::mock(ReflectionNamedType::class);

        $reflectionReturnType->shouldReceive('getName')
            ->andReturn(PromiseInterface::class);

        $this->method->shouldReceive('getReturnType')
            ->andReturn($reflectionReturnType);

        $response = $this->methodInvoker->invokeMethod($this->method, []);

        self::assertInstanceOf(PromiseInterface::class, $response);
    }

    private function prepareBasicsInteractions(bool $wrap = false): void
    {
        $this->declaringClass->shouldReceive('getAttributes')
            ->andReturn([]);

        $this->method->shouldReceive('getDeclaringClass')
            ->atLeast()
            ->once()
            ->andReturn($this->declaringClass);

        $this->method->shouldReceive('getParameters')
            ->andReturn([]);

        $reflectionAttribute = m::mock(ReflectionAttribute::class);

        $reflectionAttribute->shouldReceive('newInstance')
            ->andReturn($this->verb);

        $this->method->shouldReceive('getAttributes')
            ->with(Verb::class, ReflectionAttribute::IS_INSTANCEOF)
            ->andReturn([$reflectionAttribute]);

        $ignoredAttributes = [
            Path::class,
            Headers::class,
            Produces::class,
            Timeout::class,
            Suppress::class,
            NestedResource::class,
        ];

        if (!$wrap) {
            $ignoredAttributes[] = Unwrap::class;
        }

        foreach ($ignoredAttributes as $ignoredAttribute) {
            $this->method->shouldReceive('getAttributes')
                ->with($ignoredAttribute)
                ->andReturn([]);
        }

        $this->client->shouldReceive('requestAsync')
            ->once()
            ->with('GET', '', m::type('array'))
            ->andReturn($this->promise);
    }
}
