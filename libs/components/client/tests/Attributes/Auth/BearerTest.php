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

use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class BearerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
class BearerTest extends FeatureTestCase
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
