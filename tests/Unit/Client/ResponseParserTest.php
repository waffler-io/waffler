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

use ArrayObject;
use GuzzleHttp\Psr7\Response;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TypeError;
use Waffler\Waffler\Client\ResponseParser;

/**
 * Class ResponseParserTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\ResponseParser
 * @uses   \Waffler\Waffler\arrayGet()
 */
class ResponseParserTest extends TestCase
{
    private ResponseParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ResponseParser();
    }

    public function testItMustThrowTypeErrorIfTheGivenTypeIsNotAllowed(): void
    {
        $this->expectException(TypeError::class);

        $this->parser->parse(new Response(), 'foo');
    }

    public function testItMustReturnTheDecodedResponseForAnArrayType(): void
    {
        self::assertEquals(
            ['foo' => 'bar'],
            $this->parser->parse(new Response(body: '{"foo": "bar"}'), 'array')
        );
    }

    public function testItMustReturnTheDecodedResponseForAnArrayTypeUnwrapped(): void
    {
        self::assertEquals(
            ['foo' => 'bar'],
            $this->parser->parse(new Response(body: '{"data": {"foo": "bar"}}'), 'array', 'data')
        );
    }

    public function testItMustReturnNullForVoidOrNullType(): void
    {
        self::assertEquals(null, $this->parser->parse(new Response(), 'null'));
        self::assertEquals(null, $this->parser->parse(new Response(), 'void'));
    }

    public function testItMustReturnBooleanTrueIfTheStatusCodeIsLessThan400(): void
    {
        self::assertTrue($this->parser->parse(new Response(), 'bool'));
    }

    public function testItMustReturnBooleanFaseIfTheStatusCodeIsMoreThanEquals400(): void
    {
        self::assertFalse($this->parser->parse(new Response(400), 'bool'));
    }

    public function testItMustReturnTheBodyContentsIfTheReturnTypeIsString(): void
    {
        self::assertEquals('foo', $this->parser->parse(new Response(body: 'foo'), 'string'));
    }

    public function testItMustReturnTheStatusCodeIfTheReturnTypeIsIntOrFloatOrDouble(): void
    {
        $response = new Response();

        self::assertEquals(200, $this->parser->parse($response, 'int'));
        self::assertEquals(200, $this->parser->parse($response, 'float'));
        self::assertEquals(200, $this->parser->parse($response, 'double'));
    }

    public function testItMustReturnAnArrayObjectInstanceIfTheReturnTypeIsObjectOrArrayObject(): void
    {
        $response = new Response(body: '{"foo": "bar"}');

        $this->assertInstanceOf(
            ArrayObject::class,
            $this->parser->parse($response, 'object')
        );

        $this->assertInstanceOf(
            ArrayObject::class,
            $this->parser->parse($response, ArrayObject::class)
        );
    }

    public function testItMustReturnTheBodyOfTheResponseIfTheReturnTypeIsStreamInterface(): void
    {
        $this->assertInstanceOf(
            StreamInterface::class,
            $this->parser->parse(new Response(), StreamInterface::class)
        );
    }

    public function testItMustReturnTheResponseInterfaceIfTheReturnTypeIsResponseInterfaceSubtypeOrMixed(): void
    {
        $response = new Response();

        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($response, ResponseInterface::class)
        );
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($response, Response::class)
        );
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($response, MessageInterface::class)
        );
        self::assertInstanceOf(
            ResponseInterface::class,
            $this->parser->parse($response, 'mixed')
        );
    }
}
