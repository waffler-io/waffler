<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Unit\Client\Readers;

use Exception;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Waffler\Attributes\Auth\Basic;
use Waffler\Attributes\Auth\Bearer;
use Waffler\Attributes\Auth\Digest;
use Waffler\Attributes\Auth\Ntml;
use Waffler\Attributes\Request\Body;
use Waffler\Attributes\Request\FormData;
use Waffler\Attributes\Request\FormParam;
use Waffler\Attributes\Request\HeaderParam;
use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\JsonParam;
use Waffler\Attributes\Request\Multipart;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Request\QueryParam;
use Waffler\Attributes\Utils\RawOptions;
use Waffler\Client\Readers\Exceptions\UnableToParsePathException;
use Waffler\Client\Readers\ParameterReader;

/**
 * Class ParameterReader Test
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Readers\ParameterReader
 */
class ParameterReaderTest extends TestCase
{
    /**
     * @covers \Waffler\Client\Readers\ParameterReader::getQueryParams
     * @throws \Exception
     * @uses   \Waffler\Attributes\Request\QueryParam
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Request\Query
     */
    public function testItMustReturnTheValuesForQueryAndQueryParams(): void
    {
        $query = $this->createReflectionParameter(
            'foo',
            0,
            new Query(),
            [QueryParam::class]
        );
        $queryParam = $this->createReflectionParameter(
            'bar',
            1,
            new QueryParam('bar'),
            [Query::class]
        );

        $reader = new ParameterReader(
            [$query, $queryParam],
            [['foo' => 'foo'], 'bar']
        );

        self::assertEquals(['foo' => 'foo', 'bar' => 'bar'], $reader->getQueryParams());
    }

    /**
     * @covers \Waffler\Client\Readers\ParameterReader::getFormParams
     * @throws \Exception
     * @uses   \Waffler\Attributes\Request\FormData
     * @uses   \Waffler\Attributes\Request\FormParam
     * @uses   \Waffler\Client\AttributeChecker
     */
    public function testItMustReturnTheValuesForFormDataAndFormParams(): void
    {
        $query = $this->createReflectionParameter(
            'foo',
            0,
            new FormData(),
            [FormParam::class]
        );
        $queryParam = $this->createReflectionParameter(
            'bar',
            1,
            new FormParam('bar'),
            [FormData::class]
        );

        $reader = new ParameterReader(
            [$query, $queryParam],
            [['foo' => 'foo'], 'bar']
        );

        self::assertEquals(['foo' => 'foo', 'bar' => 'bar'], $reader->getFormParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getHeaderParams()
     * @uses   \Waffler\Attributes\Request\Body
     * @uses   \Waffler\Attributes\Request\HeaderParam
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\arrayWrap()
     * @uses   \Waffler\Attributes\Auth\Bearer
     */
    public function testGetHeaderParamsMustReturnAllHeaderParamsFromParameters(): void
    {
        $headerParam = $this->createReflectionParameter(
            'foo',
            0,
            new HeaderParam('foo'),
            [Bearer::class, Body::class]
        );
        $bearer = $this->createReflectionParameter(
            'bar',
            1,
            new Bearer(),
            [HeaderParam::class, Body::class]
        );
        $body = $this->createReflectionParameter(
            'baz',
            2,
            new Body('baz'),
            [HeaderParam::class, Bearer::class],
            'baz'
        );

        $reader = new ParameterReader(
            [$headerParam, $bearer, $body],
            ['foos', 'bar' => 'bar']
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
     * @covers \Waffler\Client\Readers\ParameterReader::getMultipartParams()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Request\Multipart
     * @uses   \Waffler\Attributes\Request\Multipart
     */
    public function testGetMultipartParamMustReturnValuesForMultipartParameters(): void
    {
        $multipart = $this->createReflectionParameter('foo', 0, new Multipart());

        $reader = new ParameterReader([$multipart], [[['foo' => 'bar']]]);

        self::assertEquals([['foo' => 'bar']], $reader->getMultipartParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getMultipartParams()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Request\Multipart
     * @uses   \Waffler\Attributes\Request\Multipart
     */
    public function testGetMultipartParamMustThrowErrorIfMoreThanOneMultipartParamIsGiven(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Only one');

        $multipart1 = $this->createReflectionParameter('foo', 0, new Multipart());
        $multipart2 = $this->createReflectionParameter('bar', 1, new Multipart());

        $reader = new ParameterReader(
            [$multipart1, $multipart2],
            [[['foo' => 'bar']], [['bar' => 'baz']]]
        );
        $reader->getMultipartParams();
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getAuthParams()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Auth\Basic
     */
    public function testGetAuthParamsMustReturnBasic(): void
    {
        $basic = $this->createReflectionParameter('foo', 0, new Basic());
        $reader = new ParameterReader([$basic], [['foo', 'bar']]);
        self::assertEquals(['foo', 'bar', 'basic'], $reader->getAuthParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getAuthParams()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Auth\Basic
     * @uses   \Waffler\Attributes\Auth\Digest
     */
    public function testGetAuthParamsMustReturnDigest(): void
    {
        $digest = $this->createReflectionParameter(
            'foo',
            0,
            new Digest(),
            [Basic::class]
        );
        $reader = new ParameterReader([$digest], [['foo', 'bar']]);
        self::assertEquals(['foo', 'bar', 'digest'], $reader->getAuthParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getAuthParams()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Auth\Basic
     * @uses   \Waffler\Attributes\Auth\Digest
     * @uses   \Waffler\Attributes\Auth\Ntml
     */
    public function testGetAuthParamsMustReturnNtml(): void
    {
        $ntml = $this->createReflectionParameter(
            'foo',
            0,
            new Ntml(),
            [Basic::class, Digest::class]
        );
        $reader = new ParameterReader([$ntml], [['foo', 'bar']]);
        self::assertEquals(['foo', 'bar', 'ntml'], $reader->getAuthParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getBodyParam()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Request\Body
     */
    public function testGetBodyParamMustReturnTheBodyString(): void
    {
        $body = $this->createReflectionParameter(
            'foo',
            0,
            new Body()
        );

        $reader = new ParameterReader([$body], ['foo']);

        self::assertEquals('foo', $reader->getBodyParam());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getRawOptions()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\Attributes\Utils\RawOptions
     */
    public function testGetRawOptionsMustReturnTheGivenArray(): void
    {
        $rawOptions = $this->createReflectionParameter(
            'foo',
            0,
            new RawOptions()
        );

        $reader = new ParameterReader([$rawOptions], [['foo' => 'bar']]);

        self::assertEquals(['foo' => 'bar'], $reader->getRawOptions());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::getJsonParams()
     * @uses   \Waffler\Attributes\Request\JsonParam
     * @uses   \Waffler\Attributes\Request\Json
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\arraySet()
     */
    public function testGetJsonParamsMustReturnTheExpectedJsonArray(): void
    {
        $json = $this->createReflectionParameter(
            'foo',
            0,
            new Json(),
            [JsonParam::class]
        );
        $jsonParam = $this->createReflectionParameter(
            'bar',
            1,
            new JsonParam('bar'),
            [Json::class]
        );

        $reader = new ParameterReader([$json, $jsonParam], [['foo' => 'foo'], 'bar']);

        self::assertEquals(['foo' => 'foo', 'bar' => 'bar'], $reader->getJsonParams());
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\arrayGet()
     * @uses   \Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustReturnAValidPathString(): void
    {
        $path1 = $this->createReflectionParameter(
            'id',
            0,
            new PathParam('id')
        );
        $path2 = $this->createReflectionParameter(
            'name',
            1,
            new PathParam('name')
        );

        $reader = new ParameterReader([$path1, $path2], [1, 'test']);

        self::assertEquals('foo/1/bar/test', $reader->parsePath('foo/{id}/bar/{name}'));
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\arrayGet()
     * @uses   \Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustThrowErrorWhenPathParameterIsNotUsed(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('is not used by any path parameter');
        $path1 = $this->createReflectionParameter(
            'id',
            0,
            new PathParam('id')
        );
        $path2 = $this->createReflectionParameter(
            'name',
            1,
            new PathParam('name')
        );

        $reader = new ParameterReader([$path1, $path2], [1, 'test']);

        $reader->parsePath('foo/{id2}/bar/{name}');
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\arrayGet()
     * @uses   \Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustThrowErrorWhenPathParameterIsRepeated(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('repeated');
        $path1 = $this->createReflectionParameter(
            'id',
            0,
            new PathParam('id')
        );
        $path2 = $this->createReflectionParameter(
            'name',
            1,
            new PathParam('id')
        );

        $reader = new ParameterReader([$path1, $path2], [1, 'test']);

        $reader->parsePath('foo/{id}/bar/{id}');
    }

    /**
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::parsePath()
     * @uses   \Waffler\Client\AttributeChecker
     * @uses   \Waffler\arrayGet()
     * @uses   \Waffler\Attributes\Request\PathParam
     */
    public function testParsePathMustThrowErrorWhenPathParameterHasNoReplacement(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('no replacement');
        $path1 = $this->createReflectionParameter(
            'id',
            0,
            new PathParam('id1')
        );
        $path2 = $this->createReflectionParameter(
            'name',
            1,
            new PathParam('id2')
        );

        $reader = new ParameterReader([$path1, $path2], [1, 'test']);

        $reader->parsePath('foo/{id1}/bar/{id2}/{id3}');
    }

    /**
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @covers \Waffler\Client\Readers\ParameterReader::loadParameterMap()
     */
    public function testMustThrowExceptionWhenRequiredParameterIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $param = m::mock(ReflectionParameter::class);
        $param->shouldReceive('getName')
            ->once()
            ->andReturn('foo');
        $param->shouldReceive('getPosition')
            ->once()
            ->andReturn(0);
        $param->shouldReceive('isDefaultValueAvailable')
            ->once()
            ->andReturn(false);
        new ParameterReader([$param], []); //@phpstan-ignore-line
    }

    //

    private function createReflectionParameter(
        ?string $name = null,
        null|int|string $position = null,
        ?object $attribute = null,
        array $emptyForAttributes = [],
        mixed $defaultValue = null
    ): ReflectionParameter|MockInterface {
        $reflection = m::mock(ReflectionParameter::class);
        if ($name) {
            $reflection->shouldReceive('getName')
                ->atLeast()->once()
                ->andReturn($name);
        }
        if ($position !== null) {
            $reflection->shouldReceive('getPosition')
                ->atLeast()->once()
                ->andReturn($position);
        }
        if ($attribute) {
            $this->prepareReflectionParameterToReturnAttribute($reflection, $attribute);
        }
        if ($defaultValue !== null) {
            $reflection->shouldReceive('isDefaultValueAvailable')
                ->atLeast()->once()
                ->andReturn(true);
            $reflection->shouldReceive('getDefaultValue')
                ->atLeast()->once()
                ->andReturn($defaultValue);
        }
        foreach ($emptyForAttributes as $emptyForAttribute) {
            $reflection->shouldReceive('getAttributes')
                ->atLeast()->once()
                ->with($emptyForAttribute)
                ->andReturn([]);
        }
        return $reflection;
    }

    /**
     * @param \ReflectionParameter|\Mockery\MockInterface $parameter
     * @param object                                      $attr
     *
     * @return void
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TAttr of object
     */
    private function prepareReflectionParameterToReturnAttribute(
        ReflectionParameter|MockInterface $parameter,
        object $attr
    ): void {
        $attrReflection = m::mock(\ReflectionAttribute::class);
        $attrReflection->shouldReceive('newInstance')
            ->atLeast()
            ->once()
            ->andReturn($attr);

        $parameter->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->with($attr::class)
            ->andReturn([$attrReflection]);
    }
}
