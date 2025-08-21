<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Auth;

use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class DigestTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
class DigestTest extends FeatureTestCase
{
    public function testRequestMustHaveDigestHeader(): void
    {
        $this->createRequestExpectation()
            ->expectGuzzleOption('auth', ['a', 'b', 'digest'])
            ->build()
            ->client
            ->testDigest(['a', 'b']);
    }
}
