<?php

namespace Waffler\Tests\Feature\Attributes\Auth;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class BasicTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class BasicTest extends FeatureTestCase
{
    public function testHeaderMustHaveAuthorizationBasic(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Authorization' => '/Basic \w*/'
            ])
            ->expectGuzzleOption('auth', ['foo', 'bar', 'basic'])
            ->build()
            ->client
            ->testBasic(['foo', 'bar']);
    }

    public function testMustThrowExceptionIfArgumentsLessThanTwo(): void
    {
        $this->expectException(\Exception::class);
        $this->client->testBasic(['foo']);
    }
}