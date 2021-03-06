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
 * Class SuppressTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class SuppressTest extends FeatureTestCase
{
    public function testErrorsMustBeSuppressed(): void
    {
        $statusCode = $this->createRequestExpectation()
            ->respondWith(new Response(404))
            ->build()
            ->client
            ->testSuppress();

        $this->assertEquals(404, $statusCode);
    }
}
