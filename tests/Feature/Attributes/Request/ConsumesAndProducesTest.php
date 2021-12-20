<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

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
