<?php

namespace Waffler\Tests\Unit\Client;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\RequestOptions;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPStan\BetterReflection\Reflection\ReflectionAttribute;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Utils\Suppress;
use Waffler\Attributes\Utils\Unwrap;
use Waffler\Attributes\Verbs\Get;
use Waffler\Client\MethodReader;
use Waffler\Client\ParameterReader;

/**
 * Class MethodReaderTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\MethodReader
 */
class MethodReaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MethodReader $methodReader;

    private ParameterReader $parameterReader;

    private ReflectionMethod $reflectionMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->methodReader = new MethodReader(
            $this->parameterReader = m::mock(ParameterReader::class)
        );
        $this->reflectionMethod = m::mock(ReflectionMethod::class);
    }

    public function testSetParameterDataMustPutTheReflectionParametersIntoParameterReader(): void
    {
        $this->parameterReader->shouldReceive('setData')
            ->once()
            ->with(m::type('array'), m::type('array'))
            ->andReturnSelf();

        $this->methodReader->setParameterReaderData([], []);
    }

    public function testIsSuppressedMustReturnFalseIfReflectionDoesNotHaveSuppressAttribute(): void
    {
        self::assertFalse($this->prepareHasAttributeTest(Suppress::class, 'isSuppressed'));
    }

    public function testMustUnwrapMustReturnFalseIfReflectionDoesNotHaveUnwrapAttribute(): void
    {
        self::assertFalse($this->prepareHasAttributeTest(Unwrap::class, 'mustUnwrap'));
    }

    public function testGetWrapperPropertyMustReturnTheNameOfJsonPropertyToUnwrap(): void
    {
        $reflectionAttribute = m::mock(\ReflectionAttribute::class);

        $reflectionAttribute->shouldReceive('newInstance')
            ->once()
            ->andReturn(new Unwrap());

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->with(Unwrap::class)
            ->andReturn([$reflectionAttribute]);

        $propName = $this->methodReader->getWrapperProperty($this->reflectionMethod);

        self::assertEquals('data', $propName);
    }

    public function testIsAsynchronousMustReturnTrueWhenTheReturnTypeIsTypeOfPromiseInterface(): void
    {
        $this->prepareGetReturnTypeTest(Promise::class);

        $is = $this->methodReader->isAsynchronous($this->reflectionMethod);

        self::assertTrue($is);
    }

    public function testGetVerbMustReturnTheAttributeThatIsInstanceOfVerbInterface(): void
    {
        $reflectionAttribute = m::mock(\ReflectionAttribute::class);
        $reflectionAttribute->shouldReceive('newInstance')
            ->once()
            ->andReturn(new Get());

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->andReturn([$reflectionAttribute]);

        $verb = $this->methodReader->getVerb($this->reflectionMethod);

        self::assertEquals('GET', $verb->getName());
    }

    public function testGetVerbMustThrowBadMethodCallExceptionIfTheReflectionMethodDoesNotHaveAnyVerbAttribute(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->andReturn([]);

        $this->methodReader->getVerb($this->reflectionMethod);
    }

    public function testIsAsyncrhonousMustReturnFalseWhenTheReturnTypeIsNotTypeOfPromiseInterface(): void
    {
        $this->prepareGetReturnTypeTest('string');

        $is = $this->methodReader->isAsynchronous($this->reflectionMethod);

        self::assertFalse($is);
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

        $type = $this->methodReader->getReturnType($this->reflectionMethod);

        self::assertEquals('mixed', $type);
    }

    public function testGetReturnTypeMustReturnMixedIfTheReturnTypeIsNotANamedReflectionType(): void
    {
        $this->prepareGetReturnTypeTest('any', false);

        $type = $this->methodReader->getReturnType($this->reflectionMethod);

        self::assertEquals('mixed', $type);
    }

    public function testParsePathMustReturnAValidPath(): void
    {
        $reflectionAttribute = m::mock(\ReflectionAttribute::class);
        $reflectionAttribute->shouldReceive('newInstance')
            ->atLeast()
            ->once()
            ->andReturn(new Path('api'));

        $declaringClass = m::mock(\ReflectionClass::class);
        $declaringClass->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->with(Path::class)
            ->andReturn([$reflectionAttribute]);

        $this->reflectionMethod->shouldReceive('getDeclaringClass')
            ->twice()
            ->andReturn($declaringClass);

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->with(Path::class)
            ->andReturn([]);

        $reflectionVerbAttribute = m::mock(ReflectionAttribute::class);
        $reflectionVerbAttribute->shouldReceive('newInstance')
            ->once()
            ->withNoArgs()
            ->andReturn(new Get('foo/{bar}'));

        $this->reflectionMethod->shouldReceive('getAttributes')
            ->withNoArgs()
            ->atLeast()
            ->once()
            ->andReturn([$reflectionVerbAttribute]);

        $this->parameterReader->shouldReceive('parsePath')
            ->once()
            ->with('api/foo/{bar}')
            ->andReturn('api/foo/1');

        $finalPath = $this->methodReader->parsePath($this->reflectionMethod);
        self::assertEquals('api/foo/1', $finalPath);
    }

    public function testGetOptionsMustReturnTheListOfGuzzleHttpOptionsFromTheParameterReader(): void
    {
        $this->reflectionMethod->shouldReceive('getAttributes')
            ->atLeast()
            ->times(4)
            ->with(m::any())
            ->andReturn([]);

        $this->parameterReader->shouldReceive('getHeaderParams')
            ->once()
            ->andReturn([]);
        $this->parameterReader->shouldReceive('getBodyParam')
            ->once()
            ->andReturn(null);
        $this->parameterReader->shouldReceive('getJsonParams')
            ->once()
            ->andReturn(null);
        $this->parameterReader->shouldReceive('getQueryParams')
            ->once()
            ->andReturn([]);
        $this->parameterReader->shouldReceive('getFormParams')
            ->once()
            ->andReturn([]);
        $this->parameterReader->shouldReceive('getMultipartParams')
            ->once()
            ->andReturn([]);
        $this->parameterReader->shouldReceive('getAuthParams')
            ->once()
            ->andReturn(null);
        $this->parameterReader->shouldReceive('getRawOptions')
            ->once()
            ->andReturn([]);

        $options = $this->methodReader->getOptions($this->reflectionMethod);

        self::assertEquals([
            RequestOptions::HTTP_ERRORS => true
        ], $options);
    }

    // private

    /**
     * @param class-string<TAttrName> $attributeName
     * @param string                  $method
     * @param array                   $returnValue
     *
     * @return bool
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @phpstan-template TAttrName of object
     */
    private function prepareHasAttributeTest(string $attributeName, string $method, array $returnValue = []): bool
    {
        $this->reflectionMethod->shouldReceive('getAttributes')
            ->once()
            ->with($attributeName)
            ->andReturn($returnValue);

        return $this->methodReader->{$method}($this->reflectionMethod);
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
}