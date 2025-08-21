<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Verbs;

use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class VerbsTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class VerbsTest extends FeatureTestCase
{
    public function testRequestMethods(): void
    {
        $verbs = ['delete', 'get', 'head', 'options', 'patch', 'post', 'put'];

        foreach ($verbs as $verb) {
            $this->createRequestExpectation()
                ->expectMethod($verb)
                ->build()
                ->client
                ->{'test' . ucfirst($verb)}();
        }
    }
}
