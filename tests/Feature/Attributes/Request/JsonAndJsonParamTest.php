<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class TestJson.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class JsonAndJsonParamTest extends FeatureTestCase
{
    public function testRequestShouldHaveJsonBody(): void
    {
        $this->createRequestExpectation()
            ->expectBody(['foo' => 'bar'])
            ->expectHeaders([
                'Content-Type' => ['application/json']
            ])
            ->build()
            ->client
            ->testJson(['foo' => 'bar']);
    }

    public function testRequestShouldHaveJsonKey(): void
    {
        $this->createRequestExpectation()
            ->expectBody(['foo' => 'bar'])
            ->expectHeaders([
                'Content-Type' => ['application/json']
            ])
            ->build()
            ->client->testJsonParam('bar');
    }

    public function testRequestShouldHaveMixedJsonArrayAndParam(): void
    {
        $this->createRequestExpectation()
            ->expectBody(['foo' => 'bar', 'baz' => 'gim'])
            ->expectHeaders([
                'Content-Type' => ['application/json']
            ])
            ->build()
            ->client
            ->testJsonAndJsonParam(['foo' => 'bar'], 'gim');
    }

    public function testJsonParamShouldOverrideJsonAttributeValue(): void
    {
        $this->createRequestExpectation()
            ->expectBody(['foo' => 'baz'])
            ->expectHeaders([
                'Content-Type' => ['application/json']
            ])
            ->build()
            ->client
            ->testJsonAndJsonParam2(['foo' => 'bar'], 'baz');
    }
}