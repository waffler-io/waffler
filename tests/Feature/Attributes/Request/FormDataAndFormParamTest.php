<?php

namespace Waffler\Tests\Feature\Attributes\Request;

use Waffler\Tests\Tools\FeatureTestCase;

/**
 * Class FormDataAndFormParamTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
class FormDataAndFormParamTest extends FeatureTestCase
{
    public function testRequestFormDataMustHaveTheGivenFormData(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => ['application/x-www-form-urlencoded']
            ])
            ->expectBody('foo=bar&baz=baz')
            ->build()
            ->client
            ->testFormData(['foo' => 'bar', 'baz' => 'baz']);
    }

    public function testRequestFormDataMustHaveTheGivenFormParams(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => ['application/x-www-form-urlencoded']
            ])
            ->expectBody('foo=bar')
            ->build()
            ->client
            ->testFormParam('bar');
    }

    public function testFormDataAndFormParamsMustBeMerged(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => ['application/x-www-form-urlencoded']
            ])
            ->expectBody('foo=bar&baz=gim')
            ->build()
            ->client
            ->testFormParamAndFormData(['foo' => 'bar'], 'gim');
    }

    public function testFormParamMustOverrideFormDataKeys(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => ['application/x-www-form-urlencoded']
            ])
            ->expectBody('foo=baz')
            ->build()
            ->client
            ->testFormParamAndFormData2(['foo' => 'bar'], 'baz');
    }
}