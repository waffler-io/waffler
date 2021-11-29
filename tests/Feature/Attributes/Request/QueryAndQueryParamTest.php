<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

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
}