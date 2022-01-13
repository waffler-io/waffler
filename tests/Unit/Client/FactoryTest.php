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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Waffler\Client\Factory;
use Waffler\Client\Proxy;
use Waffler\Generator\AnonymousClassGenerator;
use Waffler\Tests\Fixtures\Interfaces\InterfaceWithValidMethodSignature;
use Waffler\Tests\Fixtures\InvalidType;

/**
 * Class FactoryTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Factory
 * @uses \Waffler\Client\MethodInvoker
 * @uses \Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Generator\FactoryFunction
 * @uses \Waffler\Client\Proxy
 */
class FactoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testMustRejectNonInterfaceClassString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(10);
        Factory::make(InvalidType::class);
    }

    public function testMustRejectNonObjectLikeTypeName(): void
    {
        $this->expectException(\ReflectionException::class);
        Factory::make('string'); //@phpstan-ignore-line
    }

    public function testMustGenerateValidImplementationForValidInterfaces(): void
    {
        $client = Factory::make(InterfaceWithValidMethodSignature::class, []);

        self::assertInstanceOf(InterfaceWithValidMethodSignature::class, $client);
    }
}
