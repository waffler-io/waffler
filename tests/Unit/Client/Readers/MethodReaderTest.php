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

use BadMethodCallException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\RequestOptions;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Waffler\Attributes\Contracts\Verb;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Request\Timeout;
use Waffler\Attributes\Utils\Suppress;
use Waffler\Attributes\Utils\Unwrap;
use Waffler\Attributes\Verbs\Get;
use Waffler\Client\Readers\MethodReader;

/**
 * Class MethodReaderTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Readers\MethodReader
 * @uses   \Waffler\Attributes\Verbs\AbstractHttpMethod
 * @uses   \Waffler\Attributes\Verbs\Get
 * @uses   \Waffler\Attributes\Request\Path
 * @uses   \Waffler\Attributes\Request\PathParam
 * @uses   \Waffler\Client\AttributeChecker
 * @uses   \Waffler\Attributes\Utils\Unwrap
 * @uses   \Waffler\Attributes\Request\Headers
 * @uses   \Waffler\Attributes\Request\Produces
 * @uses   \Waffler\Attributes\Request\Timeout
 * @uses   \Waffler\arrayWrap()
 * @uses   \Waffler\Client\Readers\ParameterReader
 */
class MethodReaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ReflectionMethod $reflectionMethod;

    private MethodReader $methodReader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->methodReader = new MethodReader(
            $this->reflectionMethod = m::mock(ReflectionMethod::class),
            []
        );
    }

    public function testIsSuppressedMustReturnFalseIfReflectionDoesNotHaveSuppressAttribute(): void
    {
        self::assertFalse($this->prepareHasAttributeTest(
            $this->methodReader,
            Suppress::class,
            'isSuppressed'
        ));
    }

    public function testMustUnwrapMustReturnFalseIfReflectionDoesNotHaveUnwrapAttribute(): void
    {
        self::assertFalse($this->prepareHasAttributeTest(
            $this->methodReader,
            Unwrap::class,
            'mustUnwrap'
        ));
    }

    public function testGetWrapperPropertyMustReturnTheNameOfJsonPropertyToUnwrap(): void
    {
        $reflectionAttribute = m::mock(ReflectionAttribute::class);

        $reflectionAttribute->shouldReceive('newInstance')
            ->once()
            ->andReturn(new Unwrap());

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->with(Unwrap::class)
            ->andReturn([$reflectionAttribute]);

        $propName = $this->methodReader->getWrapperProperty();

        self::assertEquals('data', $propName);
    }

    public function testIsAsynchronousMustReturnTrueWhenTheReturnTypeIsTypeOfPromiseInterface(): void
    {
        $this->prepareGetReturnTypeTest(Promise::class);

        self::assertTrue($this->methodReader->isAsynchronous());
    }

    public function testGetVerbMustReturnTheAttributeThatIsInstanceOfVerbInterface(): void
    {
        $reflectionAttribute = m::mock(ReflectionAttribute::class);
        $reflectionAttribute->shouldReceive('newInstance')
            ->once()
            ->andReturn(new Get());

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->with(Verb::class, ReflectionAttribute::IS_INSTANCEOF)
            ->andReturn([$reflectionAttribute]);

        self::assertEquals('GET', $this->methodReader->getVerb()->getName());
    }

    public function testGetVerbMustThrowBadMethodCallExceptionIfTheReflectionMethodDoesNotHaveAnyVerbAttribute(): void
    {
        $this->expectException(BadMethodCallException::class);

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->andReturn([]);

        $this->methodReader->getVerb();
    }

    public function testIsAsynchronousMustReturnFalseWhenTheReturnTypeIsNotTypeOfPromiseInterface(): void
    {
        $this->prepareGetReturnTypeTest('string');

        self::assertFalse($this->methodReader->isAsynchronous());
    }

    public function testGetReturnTypeMustReturnTheNameOfTheReturnTypeDeclaredInReflectionMethod(): void
    {
        $this->prepareGetReturnTypeTest('string');

        $type = $this->methodReader->getReturnType($this->reflectionMethod);

        self::assertEquals('string', $type);
    }

    public function testGetReturnTypeMustReturnMixedIfTheMethodDoesNotHaveADelacredReturnType(): void
    {
        $this->prepareGetReturnTypeTest();

        self::assertEquals('mixed', $this->methodReader->getReturnType());
    }

    public function testGetReturnTypeMustReturnMixedIfTheReturnTypeIsNotANamedReflectionType(): void
    {
        $this->prepareGetReturnTypeTest('any', false);

        self::assertEquals('mixed', $this->methodReader->getReturnType());
    }

    public function testParsePathMustReturnAValidPath(): void
    {
        $reflectionAttribute = m::mock(ReflectionAttribute::class);
        $reflectionAttribute->shouldReceive('newInstance')
            ->atLeast()
            ->once()
            ->andReturn(new Path('api'));

        $declaringClass = m::mock(ReflectionClass::class);
        $declaringClass->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->with(Path::class)
            ->andReturn([$reflectionAttribute]);

        $this->reflectionMethod->shouldReceive('getDeclaringClass')
            ->twice()
            ->andReturn($declaringClass);

        $this->prepareHasAttributeValue(new Path('/'));

        $reflectionVerbAttribute = m::mock(ReflectionAttribute::class);
        $reflectionVerbAttribute->shouldReceive('newInstance')
            ->once()
            ->withNoArgs()
            ->andReturn(new Get('foo/{bar}'));

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->with(Verb::class, ReflectionAttribute::IS_INSTANCEOF)
            ->atLeast()
            ->once()
            ->andReturn([$reflectionVerbAttribute]);

        $this->reflectionMethod->shouldReceive('getParameters')
            ->atLeast()
            ->once()
            ->andReturn([$reflectionParameter = m::mock(ReflectionParameter::class)]);

        $reflectionParameter->shouldReceive('getName')
            ->andReturn('bar');

        $reflectionParameter->shouldReceive('getPosition')
            ->andReturn(0);

        $reflectionParameter->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->andReturn([$reflectionPathParameterAttribute = m::mock(ReflectionAttribute::class)]);

        $reflectionPathParameterAttribute->shouldReceive('newInstance')
            ->atLeast()
            ->once()
            ->andReturn(new PathParam('bar'));


        $methodReader = new MethodReader($this->reflectionMethod, [1]);

        $finalPath = $methodReader->parsePath();
        self::assertEquals('api/foo/1', $finalPath);
    }

    public function testGetOptionsMustReturnTheEmptyOfGuzzleHttpOptionsFromTheParameterReader(): void
    {
        $this->reflectionMethod->shouldReceive('getAttributes')
            ->atLeast()
            ->times(4)
            ->with(m::any())
            ->andReturn([]);

        $this->reflectionMethod->shouldReceive('getParameters')
            ->once()
            ->andReturn([]);

        $options = $this->methodReader->getOptions();

        self::assertEquals([
            RequestOptions::HTTP_ERRORS => true
        ], $options);
    }

    public function testGetOptionsMustReturnTheListOfGuzzleHttpOptionsFromTheParameterReader(): void
    {
        $this->reflectionMethod->shouldReceive('getAttributes')
            ->with(Suppress::class)
            ->atLeast()->once()
            ->andReturn([m::mock(ReflectionAttribute::class)]);
        $this->prepareHasAttributeValue(new Headers(['foo' => 'bar']));
        $this->prepareHasAttributeValue(new Produces('Application/Json'));
        $this->prepareHasAttributeValue(new Timeout(100));

        $this->reflectionMethod->shouldReceive('getParameters')
            ->once()
            ->andReturn([]);

        $options = $this->methodReader->getOptions();

        self::assertEquals([
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'foo' => ['bar'],
                'Accept' => ['Application/Json']
            ],
            RequestOptions::TIMEOUT => 100
        ], $options);
    }

    // private

    /**
     * @param \Waffler\Client\Readers\MethodReader $methodReader
     * @param class-string<TAttrName>              $attributeName
     * @param string                               $method
     * @param array                                $returnValue
     *
     * @return bool
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @phpstan-template TAttrName of object
     */
    private function prepareHasAttributeTest(
        MethodReader $methodReader,
        string $attributeName,
        string $method,
        array $returnValue = []
    ): bool {
        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->with($attributeName)
            ->andReturn($returnValue);

        return $methodReader->{$method}();
    }

    private function prepareGetReturnTypeTest(?string $typeName = null, bool $isNamedType = true): void
    {
        if ($typeName) {
            $this->reflectionMethod->shouldReceive('hasReturnType')
                ->once()
                ->andReturn(true);

            if ($isNamedType) {
                $reflectionNamedType = m::mock(\ReflectionNamedType::class);
                $reflectionNamedType->shouldReceive('getName')
                    ->once()
                    ->andReturn($typeName);

                $this->reflectionMethod->shouldReceive('getReturnType')
                    ->twice()
                    ->andReturn($reflectionNamedType);
            } else {
                $this->reflectionMethod->shouldReceive('getReturnType')
                    ->once()
                    ->andReturn(m::mock(\ReflectionUnionType::class));
            }
        } else {
            $this->reflectionMethod->shouldReceive('hasReturnType')
                ->once()
                ->andReturn(false);
        }
    }

    /**
     * @param TAttr $attributeInstance
     *
     * @return void
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TAttr of Object
     */
    private function prepareHasAttributeValue(object $attributeInstance): void
    {
        $reflectionAttr = m::mock(ReflectionAttribute::class);

        $reflectionAttr->shouldReceive('newInstance')
            ->atLeast()->once()
            ->andReturn($attributeInstance);

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->with($attributeInstance::class)
            ->atLeast()->once()
            ->andReturn([$reflectionAttr]);
    }
}
