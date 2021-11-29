<?php

namespace Waffler\Tests\Feature\Attributes\Auth;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class NtmlTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class NtmlTest extends FeatureTestCase
{
    public function testRequestMustHaveNtmlAuthorization(): void
    {
        $this->createRequestExpectation()
            ->expectGuzzleOption('auth', ['a', 'b', 'ntml'])
            ->build()
            ->client
            ->testNtml(['a', 'b']);
    }
}