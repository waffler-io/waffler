<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Request;

use Waffler\Waffler\Tests\Fixtures\FeatureTestCase;

/**
 * Class ConsumesAndProducesTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class ConsumesAndProducesTest extends FeatureTestCase
{
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
