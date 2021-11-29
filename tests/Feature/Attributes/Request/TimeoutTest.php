<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class TimeoutTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
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