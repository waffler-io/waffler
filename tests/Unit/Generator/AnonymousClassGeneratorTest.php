<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Waffler\Waffler\Generator\AnonymousClassGenerator;
use Waffler\Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Waffler\Generator\Contracts\MethodCallHandler;
use Waffler\Waffler\Tests\Fixtures\Interfaces\AnonymousClassGenerationTestCaseClientInterface;
use Waffler\Waffler\Tests\Fixtures\InvalidClient;

/**
 * @covers \Waffler\Waffler\Generator\AnonymousClassGenerator
 */
class AnonymousClassGeneratorTest extends TestCase
{
    private InterfaceInstantiator $instantiator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instantiator = new AnonymousClassGenerator();
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @uses   \Waffler\Waffler\Generator\FactoryFunction
     * @uses   \Waffler\Waffler\Generator\AnonymousClassMethod
     */
    public function testItMustInstantiateAndReturnTheSameValueAsFirstArgument(): void
    {
        $obj = $this->instantiator->instantiate(new class () implements MethodCallHandler {
            public function __call(string $name, array $arguments): mixed
            {
                return $arguments[0];
            }

            public function getReflectedInterface(): ReflectionClass
            {
                return new ReflectionClass(AnonymousClassGenerationTestCaseClientInterface::class);
            }
        });

        $this->assertEquals('foo', $obj->foo('foo'));
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @uses   \Waffler\Waffler\Generator\AnonymousClassMethod
     */
    public function testItMustThrowExceptionWhenTheGivenQualifiedNameIsNotAnInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->instantiator->instantiate(new class () implements MethodCallHandler {
            public function __call(string $name, array $arguments): mixed
            {
                return null;
            }

            public function getReflectedInterface(): ReflectionClass
            {
                return new ReflectionClass(InvalidClient::class);
            }
        });
    }
}
