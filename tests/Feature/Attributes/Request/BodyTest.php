<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class BodyTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class BodyTest extends FeatureTestCase
{
    public function testRequestMustHaveBody(): void
    {
        $body = 'foo bar baz';
        $this->createRequestExpectation()
            ->expectBody($body)
            ->build()
            ->client
            ->testBody($body);
    }
}