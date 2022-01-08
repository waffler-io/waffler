<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Unit\Client\Pipeline\Stages;

use Mockery;
use ReflectionClass;
use Waffler\Client\MethodInvoker;
use Waffler\Client\Pipeline\Stages\CreateInterfaceImplementation;
use PHPUnit\Framework\TestCase;
use Waffler\Client\Proxy;
use Waffler\Tests\Tools\Interfaces\FeatureTestCaseClient;

/**
 * Class CreateInterfaceImplementationTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Pipeline\Stages\CreateInterfaceImplementation
 * @uses \Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Client\Proxy
 * @uses \Waffler\Client\MethodInvoker
 * @uses \Waffler\Generator\FactoryFunction
 */
class CreateInterfaceImplementationTest extends TestCase
{
    public function testHandleMustReturnTheGeneratedInstanceForTheGivenInterface(): void
    {
        $instance = (new CreateInterfaceImplementation())
            ->handle(
                new Proxy(
                new ReflectionClass(FeatureTestCaseClient::class),
                Mockery::mock(MethodInvoker::class)
            )
            );

        self::assertInstanceOf(FeatureTestCaseClient::class, $instance);
    }
}
