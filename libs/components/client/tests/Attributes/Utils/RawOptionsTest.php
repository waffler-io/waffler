<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Utils;

use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class RawOptionsTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
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
