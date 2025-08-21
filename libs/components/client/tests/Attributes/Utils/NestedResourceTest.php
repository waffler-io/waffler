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
use Waffler\Component\Client\Tests\Fixtures\NestedResourceClient;

/**
 * Class NestedResourceTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
class NestedResourceTest extends FeatureTestCase
{
    public function testItMustReturnAnInstanceOfNestedResourceClient(): void
    {
        self::assertInstanceOf(NestedResourceClient::class, $this->client->nested(1));
    }

    public function testItMustInheritsThePathFromTheParentFactoryMethod(): void
    {
        $this->createRequestExpectation()
            ->expectPath('api/v1/foo/1/bar/2')
            ->build()
            ->client
            ->nested(1)
            ->getBarById(2);
    }
}
