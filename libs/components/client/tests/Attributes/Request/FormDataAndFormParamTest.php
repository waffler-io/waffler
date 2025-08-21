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

use PHPUnit\Framework\Attributes\Group;
use Waffler\Component\Client\Tests\Fixtures\FeatureTestCase;

/**
 * Class FormDataAndFormParamTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @coversNothing
 */
#[Group('waffler/client')]
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

    public function testRequestFormData2MustHaveTheGivenFormParams(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => ['application/x-www-form-urlencoded']
            ])
            ->expectBody('foo=bar')
            ->build()
            ->client
            ->testFormParam2('bar');
    }

    public function testRequestFormData3MustHaveTheGivenFormParams(): void
    {
        $this->createRequestExpectation()
            ->expectHeaders([
                'Content-Type' => ['application/x-www-form-urlencoded']
            ])
            ->expectBody('foo=bar')
            ->build()
            ->client
            ->testFormParam3('bar');
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
