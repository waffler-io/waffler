<?php

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