<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Unit\Generator;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Waffler\Generator\AnonymousClassGenerator;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;
use Waffler\Tests\Tools\Interfaces\InterfaceWithValidMethodSignature;

/**
 * @covers \Waffler\Generator\AnonymousClassGenerator
 */
class AnonymousClassGeneratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private InterfaceInstantiator $instantiator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instantiator = new AnonymousClassGenerator();
    }

    public function testItMustInstantiateAndReturnTheSameValueAsFirstArgument(): void
    {
        $callHandler = m::mock(MethodCallHandler::class);

        $value = 'foo';

        $callHandler->shouldReceive('test') //@phpstan-ignore-line
            ->once()
            ->with($value)
            ->andReturn($value);

        $callHandler->shouldReceive('getReflectedInterface')
            ->once()
            ->andReturn(new ReflectionClass(InterfaceWithValidMethodSignature::class));

        $obj = $this->instantiator->instantiate(
            $callHandler //@phpstan-ignore-line
        );

        $obj->test($value);
    }
}
