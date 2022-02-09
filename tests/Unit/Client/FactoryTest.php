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

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Waffler\Waffler\Client\Factory;
use Waffler\Waffler\Tests\Fixtures\Interfaces\InterfaceWithValidMethodSignature;
use Waffler\Waffler\Tests\Fixtures\InvalidType;

/**
 * Class FactoryTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\Factory
 * @uses \Waffler\Waffler\Client\MethodInvoker
 * @uses \Waffler\Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Waffler\Generator\FactoryFunction
 * @uses \Waffler\Waffler\Client\Proxy
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

    /**
     * @psalm-suppress UndefinedClass
     * @return void
     */
    public function testMustRejectNonObjectLikeTypeName(): void
    {
        $this->expectException(\ReflectionException::class);
        Factory::make('invalid interface name');
    }

    public function testMustGenerateValidImplementationForValidInterfaces(): void
    {
        $client = Factory::make(InterfaceWithValidMethodSignature::class, []);

        self::assertInstanceOf(InterfaceWithValidMethodSignature::class, $client);
    }
}
