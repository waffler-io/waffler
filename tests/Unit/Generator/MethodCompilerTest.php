<?php

namespace Waffler\Tests\Unit\Generator;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass;
use Waffler\Generator\Exceptions\MethodCompilingException;
use Waffler\Generator\MethodCompiler;
use PHPUnit\Framework\TestCase;
use Waffler\Tests\Tools\Interfaces\InterfaceWithInvalidMethodSignature;
use Waffler\Tests\Tools\Interfaces\InterfaceWithValidMethodSignature;

/**
 * Class MethodCompilerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Generator\MethodCompiler
 */
class MethodCompilerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithIntersectionTypesInMethodReturnType(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(3);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new MethodCompiler($reflectionClass->getMethod('intersectionReturnType'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithIntersectionTypesInParameters(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(3);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new MethodCompiler($reflectionClass->getMethod('intersectionParameter'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithPassedByReferenceParameters(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(2);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new MethodCompiler($reflectionClass->getMethod('parameterPassedByReference'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithStaticMethods(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(1);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new MethodCompiler($reflectionClass->getMethod('staticMethod'));
    }

    /**
     * @throws \Exception
     */
    public function testMustRejectInterfaceWithVariadicArguments(): void
    {
        $this->expectException(MethodCompilingException::class);
        $this->expectExceptionCode(2);
        $reflectionClass = new \ReflectionClass(InterfaceWithInvalidMethodSignature::class);
        new MethodCompiler($reflectionClass->getMethod('variadicParameter'));
    }

    /**
     * @throws \Exception
     */
    public function testMustGenerateValidPhpCode(): void
    {
        $this->expectNotToPerformAssertions();
        $reflectionClass = new \ReflectionClass(InterfaceWithValidMethodSignature::class);
        $methodRepresentation = (string)new MethodCompiler($reflectionClass->getMethod('validSignature2'));
        eval("new class { $methodRepresentation };");
    }
}
