<?php

namespace Waffler\Tests\Feature\Attributes\Auth;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class DigestTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class DigestTest extends FeatureTestCase
{
    public function testRequestMustHaveDigestHeader(): void
    {
        $this->createRequestExpectation()
            ->expectGuzzleOption('auth', ['a', 'b', 'digest'])
            ->build()
            ->client
            ->testDigest(['a', 'b']);
    }
}