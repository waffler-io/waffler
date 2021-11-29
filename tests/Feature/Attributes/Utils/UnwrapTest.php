<?php

namespace Waffler\Tests\Feature\Attributes\Utils;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Waffler\Tests\Tools\FeatureTestCase;

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