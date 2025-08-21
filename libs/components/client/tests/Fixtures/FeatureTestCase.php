<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Fixtures;

use GuzzleHttp\Handler\MockHandler;
use Psr\Http\Message\ResponseInterface;
use Waffler\Component\Client\Tests\TestCase;

/**
 * Class BaseFeatureTestCase.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class FeatureTestCase extends TestCase
{
    use CleanStart;

    protected FeatureTestCaseClient $client;

    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->factory->make(FeatureTestCaseClient::class, [
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

    protected function createRequestExpectation(): RequestExpectation
    {
        return new RequestExpectation(fn (\Closure $handler) => $this->addHandler($handler), $this);
    }
}
