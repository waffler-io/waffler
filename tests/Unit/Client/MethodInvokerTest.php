<?php

namespace Waffler\Tests\Unit\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use Waffler\Attributes\Contracts\Verb;
use Waffler\Client\MethodInvoker;
use Waffler\Client\MethodReader;
use Waffler\Client\ResponseParser;

/**
 * Class MethodInvokerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\MethodInvoker
 */
class MethodInvokerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MethodInvoker $methodInvoker;

    private MethodReader $methodReader;

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
            $this->methodReader = m::mock(MethodReader::class),
            $this->responseParser = m::mock(ResponseParser::class),
            $this->client = m::mock(ClientInterface::class)
        );

        $this->method = m::mock(ReflectionMethod::class);
        $this->verb = m::mock(Verb::class);
        $this->promise = m::mock(PromiseInterface::class);
        $this->responseInterface = m::mock(ResponseInterface::class);
    }

    public function testMustNotReturnPromiseIfTheMethodIsNotAsynchronous(): void
    {
        $this->prepareBasicsInteractions();

        $this->methodReader->shouldReceive('isAsynchronous')
            ->once()
            ->with(m::type(ReflectionMethod::class))
            ->andReturn(false);

        $this->promise->shouldReceive('wait')
            ->once()
            ->andReturn($this->responseInterface);

        $this->methodReader->shouldReceive('getReturnType')
            ->with(m::type(ReflectionMethod::class))
            ->once()
            ->andReturn('string');

        $this->methodReader->shouldReceive('mustUnwrap')
            ->once()
            ->with(m::type(ReflectionMethod::class))
            ->andReturn(false);

        $this->responseParser->shouldReceive('parse')
            ->once()
            ->with(m::type(ResponseInterface::class), 'string', false)
            ->andReturn('{}');

        $this->methodInvoker->invokeMethod($this->method, []);
    }

    public function testMustReturnPromiseIfTheMethodIsAsynchronous(): void
    {
        $this->prepareBasicsInteractions();

        $this->methodReader->shouldReceive('isAsynchronous')
            ->once()
            ->with(m::type(ReflectionMethod::class))
            ->andReturn(true);

        $response = $this->methodInvoker->invokeMethod($this->method, []);

        self::assertInstanceOf(PromiseInterface::class, $response);
    }

    private function prepareBasicsInteractions(): void
    {
        $this->method->shouldReceive('getParameters')
            ->once()
            ->andReturn([]);

        $this->methodReader->shouldReceive('setParameterReaderData')
            ->once()
            ->with(m::type('array'), m::type('array'))
            ->andReturns();

        $this->methodReader->shouldReceive('getVerb')
            ->once()
            ->with(m::type(ReflectionMethod::class))
            ->andReturn($this->verb);

        $this->verb->shouldReceive('getName')
            ->once()
            ->andReturn('GET');

        $this->methodReader->shouldReceive('parseFullPath')
            ->once()
            ->with(m::type(ReflectionMethod::class))
            ->andReturn('/');

        $this->methodReader->shouldReceive('getOptions')
            ->once()
            ->with(m::type(ReflectionMethod::class))
            ->andReturn([]);

        $this->client->shouldReceive('requestAsync')
            ->once()
            ->with('GET', '/', m::type('array'))
            ->andReturn($this->promise);
    }
}