<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Feature\Attributes\Request;

use Waffler\Waffler\Tests\Fixtures\FeatureTestCase;

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
            ->expectBody(['foo' => 'bar', 'bar' => ['baz' => 'gim']])
            ->expectHeaders([
                'Content-Type' => ['application/json']
            ])
            ->build()
            ->client->testJsonParam('bar', 'gim');
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
