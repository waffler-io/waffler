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

use Waffler\Generator\Exceptions\MethodCompilingException;
use Waffler\Generator\AnonymousClassMethod;
use PHPUnit\Framework\TestCase;
use Waffler\Tests\Tools\Interfaces\InterfaceWithInvalidMethodSignature;
use Waffler\Tests\Tools\Interfaces\InterfaceWithValidMethodSignature;

/**
 * Class MethodCompilerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Generator\AnonymousClassMethod
 */
class AnonymousClassMethodTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithIntersectionTypesInMethodReturnType(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(3);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new AnonymousClassMethod($reflectionClass->getMethod('intersectionReturnType'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithIntersectionTypesInParameters(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(3);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new AnonymousClassMethod($reflectionClass->getMethod('intersectionParameter'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithPassedByReferenceParameters(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(2);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new AnonymousClassMethod($reflectionClass->getMethod('parameterPassedByReference'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithStaticMethods(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(1);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new AnonymousClassMethod($reflectionClass->getMethod('staticMethod'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithVariadicArguments(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(2);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new AnonymousClassMethod($reflectionClass->getMethod('variadicParameter'));
    }

    /**
     * @throws \Exception
     */
    public function testMustGenerateValidPhpCode(): void
    {
        $this->expectNotToPerformAssertions();
        $reflectionClass = new \ReflectionClass(InterfaceWithValidMethodSignature::class);
        $methodRepresentation = (string)new AnonymousClassMethod($reflectionClass->getMethod('validSignature2'));
        eval("new class { $methodRepresentation };");
    }
}
