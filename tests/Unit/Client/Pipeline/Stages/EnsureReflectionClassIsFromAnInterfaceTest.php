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

use InvalidArgumentException;
use Mockery as m;
use ReflectionClass;
use Waffler\Client\Pipeline\Stages\EnsureReflectionClassIsFromAnInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class EnsureReflectionClassIsFromAnInterfaceTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Pipeline\Stages\EnsureReflectionClassIsFromAnInterface
 * @uses \Waffler\Pipeline\Contracts\StageInterface
 */
class EnsureReflectionClassIsFromAnInterfaceTest extends TestCase
{
    private ReflectionClass $reflectionClass;

    private EnsureReflectionClassIsFromAnInterface $stage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reflectionClass = m::mock(ReflectionClass::class);
        $this->stage = new EnsureReflectionClassIsFromAnInterface();
    }

    public function testItMustThrowInvalidArgumentExceptionIfTheReflectionClassIsNotInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->reflectionClass->shouldReceive('isInterface')
            ->once()
            ->andReturn(false);

        $this->reflectionClass->shouldReceive('getName')
            ->once()
            ->andReturn('foo');

        $this->stage->handle($this->reflectionClass);
    }

    public function testItMustReturnTheGivenReflectionClassIfItIsAnReflectedInterface(): void
    {
        $this->reflectionClass->shouldReceive('isInterface')
            ->once()
            ->andReturn(true);

        $this->assertEquals($this->reflectionClass, $this->stage->handle($this->reflectionClass));
    }
}
