<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Utils;

use Waffler\Waffler\Tests\Fixtures\FeatureTestCase;
use Waffler\Waffler\Tests\Fixtures\Interfaces\NestedResourceClient;

/**
 * Class NestedResourceTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class NestedResourceTest extends FeatureTestCase
{
    public function testItMustReturnAnInstanceOfNestedResourceClient(): void
    {
        $this->markTestSkipped('yet not implemented');
        self::assertInstanceOf(NestedResourceClient::class, $this->client->nested(1));
    }

    public function testItMustInheritsThePathFromTheParentFactoryMethod(): void
    {
        $this->markTestSkipped('yet not implemented');
        $this->createRequestExpectation()
            ->expectPath('api/v1/foo/1/bar/2')
            ->build()
            ->client
            ->nested(1)
            ->getBarById(2);
    }
}
