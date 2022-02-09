<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Unit\Client\Readers;

use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Waffler\Waffler\Attributes\Auth\Basic;
use Waffler\Waffler\Attributes\Auth\Bearer;
use Waffler\Waffler\Attributes\Auth\Digest;
use Waffler\Waffler\Attributes\Auth\Ntml;
use Waffler\Waffler\Attributes\Request\Body;
use Waffler\Waffler\Attributes\Request\FormData;
use Waffler\Waffler\Attributes\Request\FormParam;
use Waffler\Waffler\Attributes\Request\HeaderParam;
use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Request\Multipart;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Request\QueryParam;
use Waffler\Waffler\Attributes\Utils\RawOptions;
use Waffler\Waffler\Client\Readers\Exceptions\MultipleValuesFoundException;
use Waffler\Waffler\Client\Readers\Exceptions\UnableToParsePathException;
use Waffler\Waffler\Client\Readers\ParameterReader;
use Waffler\Waffler\Tests\Fixtures\Interfaces\ParameterReaderTestClientInterface as ClientInterface;

/**
 * Class ParameterReader Test
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\Readers\ParameterReader
 */
class ParameterReaderTest extends TestCase
{
    /**
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getQueryParams
     * @throws \Exception
     * @uses   \Waffler\Waffler\Attributes\Request\QueryParam
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Request\Query
     */
    public function testItMustReturnTheValuesForQueryAndQueryParams(): void
    {
        $reader = new ParameterReader(
            $this->getParamsOfMethod('queryAndQueryParamTest'),
            [['foo' => 'foo'], 'bar']
        );

        self::assertEquals(['foo' => 'foo', 'bar' => 'bar'], $reader->getQueryParams());
    }

    /**
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getFormParams
     * @throws \Exception
     * @uses   \Waffler\Waffler\Attributes\Request\FormData
     * @uses   \Waffler\Waffler\Attributes\Request\FormParam
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     */
    public function testItMustReturnTheValuesForFormDataAndFormParams(): void
    {
        $reader = new ParameterReader(
            $this->getParamsOfMethod('formDataAndFormParam'),
            [['foo' => 'foo'], 'bar']
        );

        self::assertEquals(['foo' => 'foo', 'bar' => 'bar'], $reader->getFormParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getHeaderParams()
     * @uses   \Waffler\Waffler\Attributes\Request\Body
     * @uses   \Waffler\Waffler\Attributes\Request\HeaderParam
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\arrayWrap()
     * @uses   \Waffler\Waffler\Attributes\Auth\Bearer
     */
    public function testGetHeaderParamsMustReturnAllHeaderParamsFromParameters(): void
    {
        $reader = new ParameterReader(
            $this->getParamsOfMethod('headerParamWithBearerAndBody'),
            ['foos', 'bar']
        );

        self::assertEquals(
            [
                'foo' => 'foos',
                'Authorization' => 'Bearer bar',
                'Content-Type' => ['baz']
            ],
            $reader->getHeaderParams()
        );
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getMultipartParams()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Request\Multipart
     * @uses   \Waffler\Waffler\Attributes\Request\Multipart
     */
    public function testGetMultipartParamMustReturnValuesForMultipartParameters(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('multipart'), [[['foo' => 'bar']]]);

        self::assertEquals([['foo' => 'bar']], $reader->getMultipartParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getMultipartParams()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Request\Multipart
     * @uses   \Waffler\Waffler\Attributes\Request\Multipart
     */
    public function testGetMultipartParamMustThrowErrorIfMoreThanOneMultipartParamIsGiven(): void
    {
        $this->expectException(MultipleValuesFoundException::class);
        $this->expectExceptionMessage('Only one');

        $reader = new ParameterReader(
            $this->getParamsOfMethod('multipleMultipart'),
            [[['foo' => 'bar']], [['bar' => 'baz']]]
        );
        $reader->getMultipartParams();
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getAuthParams()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Auth\Basic
     */
    public function testGetAuthParamsMustReturnBasic(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('basicAuth'), [['foo', 'bar']]);

        self::assertEquals(['foo', 'bar', 'basic'], $reader->getAuthParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getAuthParams()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Auth\Basic
     * @uses   \Waffler\Waffler\Attributes\Auth\Digest
     */
    public function testGetAuthParamsMustReturnDigest(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('digestAuth'), [['foo', 'bar']]);

        self::assertEquals(['foo', 'bar', 'digest'], $reader->getAuthParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getAuthParams()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Auth\Basic
     * @uses   \Waffler\Waffler\Attributes\Auth\Digest
     * @uses   \Waffler\Waffler\Attributes\Auth\Ntml
     */
    public function testGetAuthParamsMustReturnNtml(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('ntmlAuth'), [['foo', 'bar']]);

        self::assertEquals(['foo', 'bar', 'ntml'], $reader->getAuthParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getBodyParam()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Request\Body
     */
    public function testGetBodyParamMustReturnTheBodyString(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('body'), ['foo']);

        self::assertEquals('foo', $reader->getBodyParam());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getRawOptions()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\Attributes\Utils\RawOptions
     */
    public function testGetRawOptionsMustReturnTheGivenArray(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('rawOptions'), [['foo' => 'bar']]);

        self::assertEquals(['foo' => 'bar'], $reader->getRawOptions());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::getJsonParams()
     * @uses   \Waffler\Waffler\Attributes\Request\JsonParam
     * @uses   \Waffler\Waffler\Attributes\Request\Json
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\arraySet()
     */
    public function testGetJsonParamsMustReturnTheExpectedJsonArray(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('jsonAndJsonParam'), [['foo' => 'foo'], 'bar']);

        self::assertEquals(['foo' => 'foo', 'bar' => 'bar'], $reader->getJsonParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\arrayGet()
     * @uses   \Waffler\Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustReturnAValidPathString(): void
    {
        $reader = new ParameterReader($this->getParamsOfMethod('parsePath'), [1, 'test']);

        self::assertEquals('foo/1/bar/test', $reader->parsePath('foo/{id}/bar/{name}'));
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\arrayGet()
     * @uses   \Waffler\Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustThrowErrorWhenPathParameterIsNotUsed(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('is not used by any path parameter');
        $reader = new ParameterReader($this->getParamsOfMethod('parsePath'), [1, 'test']);
        $reader->parsePath('foo/{id2}/bar/{name}');
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\arrayGet()
     * @uses   \Waffler\Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustThrowErrorWhenPathParameterIsRepeated(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('repeated');
        $reader = new ParameterReader($this->getParamsOfMethod('parsePath'), [1, 'test']);
        $reader->parsePath('foo/{id}/bar/{id}');
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Waffler\Client\AttributeChecker
     * @uses   \Waffler\Waffler\arrayGet()
     * @uses   \Waffler\Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustThrowErrorWhenPathParameterHasNoReplacement(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('no replacement');
        $reader = new ParameterReader($this->getParamsOfMethod('parsePath'), [1, 'test']);
        $reader->parsePath('foo/{id}/bar/{name}/{id3}');
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Waffler\Client\Readers\ParameterReader::loadParameterMap()
     */
    public function testMustThrowExceptionWhenRequiredParameterIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ParameterReader($this->getParamsOfMethod('missingArgument'), []);
    }

    /**
     * @param string $name
     *
     * @return array<ReflectionParameter>
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getParamsOfMethod(string $name): array
    {
        return (new \ReflectionMethod(ClientInterface::class, $name))->getParameters();
    }
}
