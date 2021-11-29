<?php

namespace Waffler\Tests\Feature\Attributes\Utils;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Waffler\Tests\Tools\FeatureTestCase;

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