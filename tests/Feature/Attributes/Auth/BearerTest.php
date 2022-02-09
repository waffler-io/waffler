<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Auth;

/**
 * Class BearerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class BearerTest extends \Waffler\Waffler\Tests\Fixtures\FeatureTestCase
{
    public function testRequestMustHaveBearerAuthorizationHeader(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Authorization' => '/Bearer \w*/'
            ])
            ->build()
            ->client
            ->testBearer('abc');
    }
}
