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

use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class TimeoutTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
class TimeoutTest extends FeatureTestCase
{
    public function testRequestMustHaveATimeoutOf100(): void
    {
        $this->createRequestExpectation()
            ->expectMethod('get')
            ->expectGuzzleOption('timeout', 100)
            ->build()
            ->client
            ->testTimeout();
    }
}
