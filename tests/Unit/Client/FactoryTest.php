<?php

namespace Waffler\Tests\Unit\Client;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Waffler\Client\Factory;
use Waffler\Client\Proxy;
use Waffler\Generator\AnonymousClassGenerator;
use Waffler\Tests\Tools\Interfaces\InterfaceWithValidMethodSignature;
use Waffler\Tests\Tools\InvalidType;

/**
 * Class FactoryTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Factory
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
        $classGenerator = m::mock(AnonymousClassGenerator::class);

        $factory = new Factory(
            new ReflectionClass(InterfaceWithValidMethodSignature::class),
            $classGenerator,
            m::mock(Proxy::class)
        );

        $classGenerator->shouldReceive('instantiateFromReflection')
            ->once()
            ->with(m::type(ReflectionClass::class), m::type(Proxy::class))
            ->andReturn(m::mock(InterfaceWithValidMethodSignature::class));

        $factory->makeImplementation();
    }
}