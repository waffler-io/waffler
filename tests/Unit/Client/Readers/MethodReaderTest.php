<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Unit\Client\Readers;

use BadMethodCallException;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Waffler\Waffler\Client\Readers\MethodReader;
use Waffler\Waffler\Tests\Fixtures\Interfaces\MethodReaderTestCaseClient as Client;

/**
 * Class MethodReaderTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\Readers\MethodReader
 * @uses   \Waffler\Waffler\Attributes\Verbs\AbstractHttpMethod
 * @uses   \Waffler\Waffler\Attributes\Verbs\Get
 * @uses   \Waffler\Waffler\Attributes\Request\Path
 * @uses   \Waffler\Waffler\Attributes\Request\PathParam
 * @uses   \Waffler\Waffler\Client\AttributeChecker
 * @uses   \Waffler\Waffler\Attributes\Utils\Unwrap
 * @uses   \Waffler\Waffler\Attributes\Request\Headers
 * @uses   \Waffler\Waffler\Attributes\Request\Produces
 * @uses   \Waffler\Waffler\Attributes\Request\Timeout
 * @uses   \Waffler\Waffler\arrayWrap()
 * @uses   \Waffler\Waffler\Client\Readers\ParameterReader
 */
class MethodReaderTest extends TestCase
{
    public function testIsSuppressedMustReturnFalseIfReflectionDoesNotHaveSuppressAttribute(): void
    {
        self::assertFalse($this->newMethodReader('unsuppressed')->isSuppressed());
    }

    public function testMustUnwrapMustReturnFalseIfReflectionDoesNotHaveUnwrapAttribute(): void
    {
        self::assertFalse($this->newMethodReader('doNotWrap')->mustUnwrap());
    }

    public function testGetWrapperPropertyMustReturnTheNameOfJsonPropertyToUnwrap(): void
    {
        self::assertEquals('data', $this->newMethodReader('wrap')->getWrapperProperty());
    }

    public function testIsAsynchronousMustReturnTrueWhenTheReturnTypeIsTypeOfPromiseInterface(): void
    {
        self::assertTrue($this->newMethodReader('async')->isAsynchronous());
    }

    public function testGetVerbMustReturnTheAttributeThatIsInstanceOfVerbInterface(): void
    {
        self::assertEquals('GET', $this->newMethodReader('verbGet')->getVerb()->getName());
    }

    public function testGetVerbMustThrowBadMethodCallExceptionIfTheReflectionMethodDoesNotHaveAnyVerbAttribute(): void
    {
        $this->expectException(BadMethodCallException::class);

        $this->newMethodReader('doesNotHaveVerb')->getVerb();
    }

    public function testIsAsynchronousMustReturnFalseWhenTheReturnTypeIsNotTypeOfPromiseInterface(): void
    {
        self::assertFalse($this->newMethodReader('verbGet')->isAsynchronous());
    }

    public function testGetReturnTypeMustReturnTheNameOfTheReturnTypeDeclaredInReflectionMethod(): void
    {
        self::assertEquals('string', $this->newMethodReader('returnType')->getReturnType());
    }

    public function testGetReturnTypeMustReturnMixedIfTheMethodDoesNotHaveADelacredReturnType(): void
    {
        self::assertEquals('mixed', $this->newMethodReader('mixedReturnType')->getReturnType());
    }

    public function testGetReturnTypeMustReturnMixedIfTheReturnTypeIsNotANamedReflectionType(): void
    {
        self::assertEquals('mixed', $this->newMethodReader('unionType')->getReturnType());
    }

    public function testParsePathMustReturnAValidPath(): void
    {
        self::assertEquals(
            'foo/1/bar/2',
            $this->newMethodReader('testPath', [1, 2])->parsePath()
        );
    }

    public function testGetOptionsMustReturnTheEmptyOfGuzzleHttpOptionsFromTheParameterReader(): void
    {
        self::assertEquals([
            RequestOptions::HTTP_ERRORS => true
        ], $this->newMethodReader('noRawOptions')->getOptions());
    }

    public function testGetOptionsMustReturnTheListOfGuzzleHttpOptionsFromTheParameterReader(): void
    {
        self::assertEquals(
            [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'foo' => ['bar'],
                'Accept' => ['application/json']
            ],
            RequestOptions::TIMEOUT => 100
        ],
            $this->newMethodReader('withManyOptions')->getOptions()
        );
    }

    // private

    /**
     * @throws \ReflectionException
     */
    private function newMethodReader(string $method, array $arguments = []): MethodReader
    {
        return new MethodReader(
            new ReflectionMethod(Client::class, $method),
            $arguments
        );
    }
}
