<?php

namespace Waffler\Tests\Tools;

use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Waffler\Client\Factory;
use Waffler\Tests\Tools\Interfaces\FeatureTestCaseClient;

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
     * @return \Waffler\Tests\Tools\RequestExpectation<static>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    protected function createRequestExpectation(): RequestExpectation
    {
        // @phpstan-ignore-next-line
        return new RequestExpectation(function (\Closure $handler) {
            $this->addHandler($handler);
        }, $this);
    }
}