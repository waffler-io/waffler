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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Utils\NestedResource;
use Waffler\Client\MethodInvoker;
use Waffler\Client\Proxy;
use Mockery as m;
use Waffler\Tests\Fixtures\Interfaces\NestedResourceClient;

/**
 * Class ProxyTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Proxy
 * @uses \Waffler\Generator\FactoryFunction
 * @uses \Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Client\Readers\ParameterReader
 * @uses \Waffler\Client\Readers\MethodReader
 * @uses \Waffler\Client\MethodInvoker
 * @uses \Waffler\Attributes\Request\Path
 * @uses \Waffler\arrayWrap()
 */
class ProxyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Proxy $proxy;

    private ReflectionClass $reflectionClass;

    private MethodInvoker $methodInvoker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->proxy = new Proxy(
            $this->reflectionClass = m::mock(ReflectionClass::class),
            $this->methodInvoker = m::mock(MethodInvoker::class),
            []
        );
    }

    public function testMustProxyAllCallsToMethodInvoker(): void
    {
        $this->reflectionClass->shouldReceive('hasMethod')
            ->once()
            ->with('foo')
            ->andReturn(true);

        $this->reflectionClass->shouldReceive('getMethod')
            ->once()
            ->with('foo')
            ->andReturn($method = m::mock(ReflectionMethod::class));

        $method->shouldReceive('getAttributes')
            ->atLeast()->once()
            ->with(NestedResource::class)
            ->andReturn([]);

        $this->methodInvoker->shouldReceive('invokeMethod')
            ->once()
            ->with(m::type(ReflectionMethod::class), ['bar', 'baz'], [])
            ->andReturn(true);

        $this->proxy->foo('bar', 'baz');
    }

    public function testMustThrowErrorWhenAnUndefinedMethodIsCalled(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->reflectionClass->shouldReceive('hasMethod')
            ->once()
            ->with('foo')
            ->andReturn(false);

        $this->proxy->foo();
    }

    public function testGetReflectedInterfaceMustReturnAnInstanceOfReflectionClass(): void
    {
        self::assertEquals($this->reflectionClass, $this->proxy->getReflectedInterface());
    }

    public function testItMustThrowExceptionWhenTheMethodReturnsNestedResourceButTheReturnTypeIsInvalid(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('must return an interface type');

        $this->reflectionClass->shouldReceive('hasMethod')
            ->once()
            ->with('foo')
            ->andReturn(true);

        $this->reflectionClass->shouldReceive('getMethod')
            ->once()
            ->with('foo')
            ->andReturn($method = m::mock(ReflectionMethod::class));

        $method->shouldReceive('getReturnType')
            ->atLeast()->once()
            ->andReturn(null);

        $method->shouldReceive('getAttributes')
            ->atLeast()->once()
            ->with(NestedResource::class)
            ->andReturn([m::mock(\ReflectionAttribute::class)]);

        $this->proxy->foo('bar', 'baz');
    }

    /**
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @uses \Waffler\Client\Factory
     */
    public function testItMustReturnAnotherClientInterfaceWhenTheMethodReturnNestedResource(): void
    {
        $this->reflectionClass->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->andReturn([]);

        $this->reflectionClass->shouldReceive('hasMethod')
            ->once()
            ->with('foo')
            ->andReturn(true);

        $this->reflectionClass->shouldReceive('getMethod')
            ->once()
            ->with('foo')
            ->andReturn($method = m::mock(ReflectionMethod::class));

        $method->shouldReceive('getDeclaringClass')
            ->atLeast()->once()
            ->andReturn($this->reflectionClass);

        $method->shouldReceive('getReturnType')
            ->atLeast()->once()
            ->andReturn($returnType = m::mock(ReflectionNamedType::class));

        $method->shouldReceive('getAttributes')
            ->atLeast()->once()
            ->with(NestedResource::class)
            ->andReturn([m::mock(\ReflectionAttribute::class)]);

        $returnType->shouldReceive('getName')
            ->atLeast()
            ->once()
            ->andReturn(NestedResourceClient::class);

        $method->shouldReceive('getAttributes')
            ->atLeast()
            ->once()
            ->with(Path::class)
            ->andReturn([$reflectionPathAttribute = m::mock(\ReflectionAttribute::class)]);

        $method->shouldReceive('getParameters')
            ->atLeast()
            ->once()
            ->andReturn([]);

        $reflectionPathAttribute->shouldReceive('newInstance')
            ->andReturn(new Path('foo'));

        $this->assertInstanceOf(NestedResourceClient::class, $this->proxy->foo());
    }
}
