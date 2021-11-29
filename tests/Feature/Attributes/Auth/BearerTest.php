<?php

namespace Waffler\Tests\Feature\Attributes\Auth;

/**
 * Class BearerTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class BearerTest extends \Waffler\Tests\Tools\FeatureTestCase
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