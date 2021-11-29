<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class PathAndPathParamTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class PathAndPathParamTest extends FeatureTestCase
{
    public function testRequestMustBeSentToTheGivenPath(): void
    {
        $this->createRequestExpectation()
            ->expectPath('api/v1/foo/bar/baz')
            ->build()
            ->client
            ->testPath();
    }

    public function testRequestPathMustBeProperlyFormattedWithPathParams(): void
    {
        $this->createRequestExpectation()
            ->expectPath('api/v1/foo/1/bar/2')
            ->build()
            ->client
            ->testPathAndPathParam(1, 2);
    }

    public function testVerbPathMustBeAppendedAfterInterfaceAndMethodPath(): void
    {
        $this->createRequestExpectation()
            ->expectPath('api/v1/foo/1/bar/2/baz/3')
            ->build()
            ->client
            ->testPathAndPathParam2(1, 2, 3);
    }
}