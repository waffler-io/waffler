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

use Exception;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

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

    public function testOptionalPathParameterArgumentsMustBeAllowed(): void
    {
        $this->createRequestExpectation()
            ->expectPath('api/v1/foo/')
            ->build()
            ->client
            ->testOptionalPathParam(null);
    }
}
