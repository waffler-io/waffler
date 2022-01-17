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

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use ReflectionClass;
use ReflectionMethod;
use Waffler\Client\MethodInvoker;
use Waffler\Client\Proxy;
use Waffler\Tests\Fixtures\Interfaces\ProxyTestClientInterface;

/**
 * Class ProxyTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Proxy
 * @uses \Waffler\Client\Factory
 * @uses \Waffler\Generator\FactoryFunction
 * @uses \Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Client\Readers\ParameterReader
 * @uses \Waffler\Client\Readers\MethodReader
 * @uses \Waffler\Client\MethodInvoker
 * @uses \Waffler\Attributes\Request\Path
 * @uses \Waffler\Attributes\Verbs\Get
 * @uses \Waffler\Client\AttributeChecker
 * @uses \Waffler\Client\ResponseParser
 * @uses \Waffler\Attributes\Request\PathParam
 * @uses \Waffler\Attributes\Verbs\AbstractHttpMethod
 * @uses \Waffler\arrayWrap()
 */
class ProxyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Waffler\Client\Proxy<ProxyTestClientInterface>
     */
    private Proxy $proxy;

    private MethodInvoker|MockInterface $methodInvoker;

    private MockHandler $handlerStack;

    protected function setUp(): void
    {
        parent::setUp();
        $this->proxy = new Proxy(
            new ReflectionClass(ProxyTestClientInterface::class),
            $this->methodInvoker = m::mock(MethodInvoker::class), //@phpstan-ignore-line
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
