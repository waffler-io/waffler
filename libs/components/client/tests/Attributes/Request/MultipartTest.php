<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Request;

use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

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
