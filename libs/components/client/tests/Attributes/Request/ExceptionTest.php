<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Request;

use Exception;
use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\CleanStart;
use Waffler\Component\Client\Tests\TestCase;
use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Attributes\Verbs\Get;
use Waffler\Component\Generator\Exceptions\UnableToParsePathException;

/**
 * Class PathAndPathParamTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
class ExceptionTest extends TestCase
{
    use CleanStart;

    public function testRepeatedParameterMustThrowException(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('The path parameter "foo" is repeated');

        $this->factory->make(RepeatedPathParameterInterface::class);
    }

    public function testParameterWithNoReplacementMustThrowException(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('The path parameter "{bar}" has no replacement.');

        $this->factory->make(ParameterWithNoReplacementInterface::class);
    }

    public function testUnusedPathParametersMustThrowAnException(): void
    {
        $this->expectException(UnableToParsePathException::class);
        $this->expectExceptionMessage('The argument "unused" is not used by any path parameter.');

        $this->factory->make(UnusedPathParameterInterface::class);
    }
}

interface RepeatedPathParameterInterface
{
    #[Get('foo/{foo}/bar/{foo}')]
    public function test(#[PathParam('foo')] int $foo);
}

interface ParameterWithNoReplacementInterface
{
    #[Get('/{foo}/bar/{bar}')]
    public function test(#[PathParam('foo')] int $foo);
}

interface UnusedPathParameterInterface
{
    #[Get('foo/{foo}')]
    public function test(#[PathParam] int $unused): void;
}
