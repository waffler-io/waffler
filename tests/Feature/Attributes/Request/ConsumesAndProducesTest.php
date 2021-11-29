<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class ConsumesAndProducesTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class ConsumesAndProducesTest extends FeatureTestCase
{
    public function testRequestMustHaveAcceptHeader(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => 'application/json'
            ])
            ->build()
            ->client
            ->testConsumes();
    }

    public function testRequestMustHaveContentTypeHeader(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Accept' => 'application/json'
            ])
            ->build()
            ->client
            ->testProduces();
    }
}