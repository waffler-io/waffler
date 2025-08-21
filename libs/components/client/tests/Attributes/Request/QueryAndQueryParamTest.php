<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Attributes\Request;

use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class TestQuery.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class QueryAndQueryParamTest extends FeatureTestCase
{
    public function testRequestShouldReceiveQueryString(): void
    {
        $this->createRequestExpectation()
            ->expectQueryString([
                'foo' => 'bar',
                'baz' => 'gim'
            ])
            ->build()
            ->client
            ->testQuery([
                'foo' => 'bar',
                'baz' => 'gim'
            ]);
    }

    public function testRequestUrlMustHaveTheGivenQueryParam(): void
    {
        $this->createRequestExpectation()
            ->expectQueryString(['foo' => 'bar'])
            ->build()
            ->client
            ->testQueryParam('bar');
    }

    public function testQueryAndQueryParamMustBeMerged(): void
    {
        $this->createRequestExpectation()
            ->expectQueryString(['foo' => 'bar', 'baz' => 'gim'])
            ->build()
            ->client
            ->testQueryAndQueryParam(['foo' => 'bar'], 'gim');
    }

    public function testQueryParamMustOverrideQueryAttributeValue(): void
    {
        $this->createRequestExpectation()
            ->expectQueryString(['foo' => 'baz'])
            ->build()
            ->client
            ->testQueryAndQueryParam2(['foo' => 'bar'], 'baz');
    }

    public function testQueryParamMustSendArrays(): void
    {
        $this->createRequestExpectation()
            ->expectQueryString(['search' => ['foo', 'bar']])
            ->build()
            ->client
            ->testQueryArray(['foo', 'bar']);
    }
}
