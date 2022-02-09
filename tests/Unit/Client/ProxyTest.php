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

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use ReflectionClass;
use ReflectionMethod;
use Waffler\Waffler\Client\MethodInvoker;
use Waffler\Waffler\Client\Proxy;
use Waffler\Waffler\Tests\Fixtures\Interfaces\ProxyTestClientInterface;

/**
 * Class ProxyTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\Proxy
 * @uses \Waffler\Waffler\Client\Factory
 * @uses \Waffler\Waffler\Generator\FactoryFunction
 * @uses \Waffler\Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Waffler\Client\Readers\ParameterReader
 * @uses \Waffler\Waffler\Client\Readers\MethodReader
 * @uses \Waffler\Waffler\Client\MethodInvoker
 * @uses \Waffler\Waffler\Attributes\Request\Path
 * @uses \Waffler\Waffler\Attributes\Verbs\Get
 * @uses \Waffler\Waffler\Client\AttributeChecker
 * @uses \Waffler\Waffler\Client\ResponseParser
 * @uses \Waffler\Waffler\Attributes\Request\PathParam
 * @uses \Waffler\Waffler\Attributes\Verbs\AbstractHttpMethod
 * @uses \Waffler\Waffler\arrayWrap()
 */
class ProxyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Waffler\Waffler\Client\Proxy<ProxyTestClientInterface>
     */
    private Proxy $proxy;

    private MethodInvoker|MockInterface $methodInvoker;

    private MockHandler $handlerStack;

    protected function setUp(): void
    {
        parent::setUp();
        $this->proxy = new Proxy(
            new ReflectionClass(ProxyTestClientInterface::class),
            $this->methodInvoker = m::mock(MethodInvoker::class),
            [
                'handler' => $this->handlerStack = new MockHandler()
            ]
        );
    }

    public function testMustProxyAllCallsToMethodInvoker(): void
    {
        $this->methodInvoker->shouldReceive('invokeMethod')
            ->once()
            ->with(m::type(ReflectionMethod::class), ['bar', 'baz'], [])
            ->andReturn(true);

        $this->proxy->foo('bar', 'baz');
    }

    public function testMustThrowErrorWhenAnUndefinedMethodIsCalled(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->proxy->undefinedMethod();
    }

    public function testGetReflectedInterfaceMustReturnAnInstanceOfReflectionClass(): void
    {
        self::assertInstanceOf(ReflectionClass::class, $this->proxy->getReflectedInterface());
    }

    public function testItMustThrowExceptionWhenTheMethodReturnsNestedResourceButTheReturnTypeIsInvalid(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('must return an interface type');

        $this->proxy->invalidNestedResource();
    }

    public function testItMustThrowExceptionWhenTheNestedResourceMethodDoesNotHaveReturnType(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('must return an interface type');

        $this->proxy->invalidNestedResource2();
    }

    /**
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function testItMustReturnAnotherClientInterfaceWhenTheMethodReturnNestedResource(): void
    {
        $nestedResource = $this->proxy->validNestedResource();

        self::assertInstanceOf(ProxyTestClientInterface::class, $nestedResource);
    }

    public function testItMustConcatenateTheParsedPathOfParentInterfaceWithTheNestedResourceUri(): void
    {
        $this->handlerStack->append(function (RequestInterface $request) {
            self::assertEquals('foo/foo2/bar/baz', $request->getUri()->getPath());
            return new Response();
        });

        $this->proxy->validNestedResourceWithPath('foo')
            ->validNestedResourceWithPath('foo2')
            ->foo('bar', 'baz');
    }
}
