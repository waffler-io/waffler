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

use ArrayObject;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TypeError;
use Waffler\Client\ResponseParser;

/**
 * Class ResponseParserTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\ResponseParser
 * @uses   \Waffler\arrayGet()
 */
class ResponseParserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ResponseParser $parser;

    private ResponseInterface $response;

    private StreamInterface $stream;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ResponseParser();
        $this->response = m::mock(ResponseInterface::class);
        $this->stream = m::mock(StreamInterface::class);
    }

    public function testItMustThrowTypeErrorIfTheGivenTypeIsNotAllowed(): void
    {
        $this->expectException(TypeError::class);

        $this->parser->parse($this->response, 'foo');
    }

    public function testItMustReturnTheDecodedResponseForAnArrayType(): void
    {
        $this->response->shouldReceive('getBody')
            ->atLeast()->once()
            ->andReturn($this->stream);
        $this->stream->shouldReceive('getContents')
            ->atLeast()->once()
            ->andReturn('{"foo": "bar"}');

        self::assertEquals(
            ['foo' => 'bar'],
            $this->parser->parse($this->response, 'array')
        );
    }

    public function testItMustReturnTheDecodedResponseForAnArrayTypeUnwrapped(): void
    {
        $this->response->shouldReceive('getBody')
            ->atLeast()->once()
            ->andReturn($this->stream);
        $this->stream->shouldReceive('getContents')
            ->atLeast()->once()
            ->andReturn('{"data": {"foo": "bar"}}');

        self::assertEquals(
            ['foo' => 'bar'],
            $this->parser->parse($this->response, 'array', 'data')
        );
    }

    public function testItMustReturnNullForVoidOrNullType(): void
    {
        self::assertEquals(null, $this->parser->parse($this->response, 'null'));
        self::assertEquals(null, $this->parser->parse($this->response, 'void'));
    }

    public function testItMustReturnBooleanTrueIfTheStatusCodeIsLessThan400(): void
    {
        $this->response->shouldReceive('getStatusCode')
            ->atLeast()->once()
            ->andReturn(200);

        self::assertTrue($this->parser->parse($this->response, 'bool'));
    }

    public function testItMustReturnBooleanFaseIfTheStatusCodeIsMoreThanEquals400(): void
    {
        $this->response->shouldReceive('getStatusCode')
            ->atLeast()->once()
            ->andReturn(400);

        self::assertFalse($this->parser->parse($this->response, 'bool'));
    }

    public function testItMustReturnTheBodyContentsIfTheReturnTypeIsString(): void
    {
        $this->response->shouldReceive('getBody')
            ->atLeast()->once()
            ->andReturn($this->stream);

        $this->stream->shouldReceive('getContents')
            ->atLeast()->once()
            ->andReturn('foo');

        self::assertEquals('foo', $this->parser->parse($this->response, 'string'));
    }

    public function testItMustReturnTheStatusCodeIfTheReturnTypeIsIntOrFloatOrDouble(): void
    {
        $this->response->shouldReceive('getStatusCode')
            ->andReturn(200);

        self::assertEquals(200, $this->parser->parse($this->response, 'int'));
        self::assertEquals(200, $this->parser->parse($this->response, 'float'));
        self::assertEquals(200, $this->parser->parse($this->response, 'double'));
    }

    public function testItMustReturnAnArrayObjectInstanceIfTheReturnTypeIsObjectOrArrayObject(): void
    {
        $this->response->shouldReceive('getBody')
            ->andReturn($this->stream);

        $this->stream->shouldReceive('getContents')
            ->atLeast()->once()
            ->andReturn('{"foo": "bar"}');

        $this->assertInstanceOf(
            ArrayObject::class,
            $this->parser->parse($this->response, 'object')
        );

        $this->assertInstanceOf(
            ArrayObject::class,
            $this->parser->parse($this->response, ArrayObject::class)
        );
    }

    public function testItMustReturnTheBodyOfTheResponseIfTheReturnTypeIsStreamInterface(): void
    {
        $this->response->shouldReceive('getBody')
            ->once()
            ->andReturn($this->stream);

        $this->assertInstanceOf(
            StreamInterface::class,
            $this->parser->parse($this->response, StreamInterface::class)
        );
    }

    public function testItMustReturnTheResponseInterfaceIfTheReturnTypeIsResponseInterfaceSubtypeOrMixed(): void
    {
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($this->response, ResponseInterface::class)
        );
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($this->response, Response::class)
        );
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($this->response, MessageInterface::class)
        );
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($this->response, 'mixed')
        );
    }
}
