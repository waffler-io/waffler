<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Auth;

use Waffler\Waffler\Tests\Fixtures\FeatureTestCase;

/**
 * Class NtmlTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class NtmlTest extends FeatureTestCase
{
    public function testRequestMustHaveNtmlAuthorization(): void
    {
        $this->createRequestExpectation()
            ->expectGuzzleOption('auth', ['a', 'b', 'ntml'])
            ->build()
            ->client
            ->testNtml(['a', 'b']);
    }
}
