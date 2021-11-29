<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class MultipartTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class MultipartTest extends FeatureTestCase
{
    public function testRequestMustBeSentWithMultipartFormData(): void
    {
        $data = [
            [
                'name' => 'foo',
                'contents' => 'bar'
            ],
            [
                'name' => 'baz',
                'contents' => 'gim'
            ]
        ];

        $this->createRequestExpectation()
            ->expectMultipartFormData($data)
            ->build()
            ->client
            ->testMultipart($data);
    }
}