<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Auth;

use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class BasicTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class BasicTest extends FeatureTestCase
{
    public function testHeaderMustHaveAuthorizationBasic(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Authorization' => '/Basic \w*/'
            ])
            ->expectGuzzleOption('auth', ['foo', 'bar', 'basic'])
            ->build()
            ->client
            ->testBasic(['foo', 'bar']);
    }

    public function testMustThrowExceptionIfArgumentsLessThanTwo(): void
    {
        $this->expectException(\Exception::class);
        $this->client->testBasic(['foo']);
    }
}
