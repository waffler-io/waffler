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

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class SuppressTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
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
