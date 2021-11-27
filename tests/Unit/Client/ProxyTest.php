<?php

namespace Waffler\Tests\Unit\Client;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Waffler\Client\MethodInvoker;
use Waffler\Client\Proxy;
use Mockery as m;

/**
 * Class ProxyTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Proxy
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
            $this->methodInvoker = m::mock(MethodInvoker::class)
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
            ->andReturn(m::mock(ReflectionMethod::class));

        $this->methodInvoker->shouldReceive('invokeMethod')
            ->once()
            ->with(m::type(ReflectionMethod::class), ['bar', 'baz'])
            ->andReturn(true);

        $this->proxy->foo('bar', 'baz');
    }

    public function testMustThrowErrorWhenAnUndefinedMethodIsCalled(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->reflectionClass->shouldReceive('hasMethod')
            ->once()
            ->with('foo')
            ->andReturn('false');

        $this->proxy->foo();
    }
}