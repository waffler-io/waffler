<?php

namespace Waffler\Tests\Feature\Attributes\Verbs;

use Waffler\Tests\Tools\FeatureTestCase;

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