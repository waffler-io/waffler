<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Fixtures;

use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Waffler\Waffler\Client\Factory;
use Waffler\Waffler\Tests\Fixtures\Interfaces\FeatureTestCaseClient;

/**
 * Class BaseFeatureTestCase.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class FeatureTestCase extends TestCase
{
    protected FeatureTestCaseClient $client;

    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Factory::make(FeatureTestCaseClient::class, [
            'handler' => $this->mockHandler = new MockHandler()
        ]);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface|\Closure ...$handlers
     *
     * @return $this
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    protected function addHandler(ResponseInterface|\Closure...$handlers): self
    {
        $this->mockHandler->append(...$handlers);

        return $this;
    }

    /**
     * @return \Waffler\Waffler\Tests\Fixtures\RequestExpectation
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    protected function createRequestExpectation(): RequestExpectation
    {
        return new RequestExpectation(fn (\Closure $handler) => $this->addHandler($handler), $this);
    }
}
