<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Utils;

use GuzzleHttp\Psr7\Response;
use Waffler\Waffler\Tests\Fixtures\FeatureTestCase;

/**
 * Class UnwrapTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class UnwrapTest extends FeatureTestCase
{
    public function testResponseDataMustBeUnwrapped(): void
    {
        $data = $this->createRequestExpectation()
            ->respondWith(new Response(body: (string)json_encode(['data' => ['contents' => ['foo' => 'bar']]])))
            ->build()
            ->client
            ->testUnwrap();

        $this->assertEquals(['foo' => 'bar'], $data);
    }
}
