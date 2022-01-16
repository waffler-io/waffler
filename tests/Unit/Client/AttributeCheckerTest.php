<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Unit\Client;

use PHPUnit\Framework\TestCase;
use Waffler\Attributes\Auth\Basic;
use Waffler\Attributes\Auth\Bearer;
use Waffler\Attributes\Auth\Digest;
use Waffler\Attributes\Auth\Ntml;
use Waffler\Attributes\Request\FormData;
use Waffler\Attributes\Request\HeaderParam;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\Json;
use Waffler\Attributes\Request\JsonParam;
use Waffler\Attributes\Request\Multipart;
use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Request\Query;
use Waffler\Attributes\Request\QueryParam;
use Waffler\Attributes\Utils\RawOptions;
use Waffler\Client\AttributeChecker;

/**
 * Class AttributeCheckerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\AttributeChecker
 */
class AttributeCheckerTest extends TestCase
{
    /**
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function testCheckMustDoNothing(): void
    {
        $this->expectNotToPerformAssertions();

        try {
            $this->createExpectationsFor([
                [
                    [Basic::class, Digest::class, Ntml::class],
                    [['foo', 'bar']]
                ],
                [
                    [Query::class, Json::class, Headers::class, Multipart::class, FormData::class, RawOptions::class],
                    [[]]
                ],
                [
                    [Bearer::class, PathParam::class, QueryParam::class],
                    ['foo', 1, null]
                ],
                [
                    [HeaderParam::class],
                    ['foo', null]
                ],
                [
                    [JsonParam::class],
                    ['foo', 1, null, [], 1.5]
                ]
            ]);
        } catch (\InvalidArgumentException) {
            self::assertTrue(true);
        }
    }

    public function testCheckMustThrowExceptions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AttributeChecker::check(Bearer::class, [['foo']]);
    }

    public function testHeaderParamsMustThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AttributeChecker::check(Basic::class, ['foo']);
    }

    /**
     * @param array $items
     *
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function createExpectationsFor(array $items): void //@phpstan-ignore-line
    {
        foreach ($items as [$attributes, $values]) {
            foreach ($attributes as $attribute) {
                foreach ($values as $value) {
                    AttributeChecker::check($attribute, $value);
                }
            }
        }
    }
}
