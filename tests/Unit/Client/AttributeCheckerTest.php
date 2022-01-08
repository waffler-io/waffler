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
use Waffler\Attributes\Request\HeaderParam;
use Waffler\Attributes\Request\JsonParam;
use Waffler\Attributes\Request\Query;
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
     * @covers \Waffler\Client\AttributeChecker::check
     */
    public function testCheckMustDoNothing(): void
    {
        $this->expectNotToPerformAssertions();

        AttributeChecker::check(Bearer::class, 'foo');
        AttributeChecker::check(Basic::class, ['one', 'two']);
        AttributeChecker::check(Query::class, []);
        AttributeChecker::check(HeaderParam::class, '');
        AttributeChecker::check(JsonParam::class, 1);
    }
}
