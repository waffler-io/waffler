<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Feature\Attributes\Utils;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class RawOptionsTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class RawOptionsTest extends FeatureTestCase
{
    public function testGuzzleClientMustReceiveRawGuzzleOptions(): void
    {
        $this->createRequestExpectation()
            ->expectGuzzleOption('foo', 'bar')
            ->expectGuzzleOption('baz', 'gim')
            ->build()
            ->client
            ->testRawOptions(['foo' => 'bar', 'baz' => 'gim']);
    }
}
