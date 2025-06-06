<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Request;

use Exception;
use PHPUnit\Framework\TestCase;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Verbs\Get;
use Waffler\Waffler\Implementation\Exceptions\UnableToParsePathException;
use Waffler\Waffler\Tests\Fixtures\CleanStart;

/**
 * Class PathAndPathParamTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
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
