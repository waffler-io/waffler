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

use Closure;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

use function json_decode;
use function Waffler\Component\Helpers\arrayWrap;

/**
 * Class RequestExpectation.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class RequestExpectation
{
    private Response $response;

    /**
     * @var array<\Closure>
     */
    private array $expectations = [];

    /**
     * @param \Closure      $callback
     * @param FeatureTestCase $testCase
     */
    public function __construct(
        private Closure $callback,
        private FeatureTestCase $testCase
    ) {
        $this->response = new Response();
    }

    /**
     * @param string $toBe
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectMethod(string $toBe): self
    {
        return $this->addExpectation(function (RequestInterface $request) use ($toBe) {
            $toBe = strtoupper($toBe);
            $this->testCase::assertEquals(
                $toBe,
                $request->getMethod(),
                "Failed asserting the request method to be $toBe, {$request->getMethod()} received."
            );
        });
    }

    /**
     * @param array<string, string|int|array<int|string>>|string $query
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectQueryString(array|string $query): self
    {
        return $this->addExpectation(function (RequestInterface $request) use ($query) {
            $query = is_array($query) ? http_build_query($query) : $query;

            $this->testCase::assertEquals($query, $request->getUri()->getQuery());
        });
    }

    /**
     * @param array<string, array<string|int>|string|callable> $headers
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectHeaders(array $headers): self
    {
        return $this->addExpectation(function (RequestInterface $request) use ($headers) {
            foreach ($headers as $headerName => $header) {
                $hasHeader = $request->hasHeader($headerName);
                $this->testCase::assertTrue(
                    $hasHeader,
                    "Failed asserting the request has a header named \"$headerName\"."
                );

                $expectedHeaders = arrayWrap($header);

                foreach ($expectedHeaders as $expectedIndex => $expectedHeader) {
                    if (is_callable($expectedHeader)) {
                        $expectedHeader($request->getHeader($headerName)[$expectedIndex]);
                    } elseif (str_starts_with($expectedHeader, '/') && str_ends_with($expectedHeader, '/')) {
                        $this->testCase::assertMatchesRegularExpression(
                            $expectedHeader,
                            $request->getHeader($headerName)[$expectedIndex]
                        );
                    } else {
                        $this->testCase::assertContains(
                            $expectedHeader,
                            $request->getHeader($headerName)
                        );
                    }
                }
            }
        });
    }

    /**
     * @param string $toBe
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectPath(string $toBe): self
    {
        return $this->addExpectation(function (RequestInterface $request) use ($toBe) {
            $this->testCase::assertEquals($toBe, $request->getUri()->getPath());
        });
    }

    /**
     * @param string $key
     * @param mixed  $toBe
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectGuzzleOption(string $key, mixed $toBe): self
    {
        return $this->addExpectation(function (RequestInterface $request, array $options) use ($key, $toBe) {
            $this->testCase::assertArrayHasKey($key, $options);

            $this->testCase::assertEquals(
                $toBe,
                $options[$key]
            );
        });
    }

    /**
     * @param array<int|string, mixed>|string $toBe
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectBody(array|string $toBe): self
    {
        return $this->addExpectation(function (RequestInterface $request) use ($toBe) {
            $isArray = is_array($toBe);
            $toBe = $isArray ? json_encode($toBe) : $toBe;

            $this->testCase::assertEquals(
                $toBe,
                $isArray
                    ? json_encode(json_decode($request->getBody()->getContents()))
                    : $request->getBody()->getContents()
            );
        });
    }

    /**
     * @param array<array<string,mixed>> $values
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function expectMultipartFormData(array $values): self
    {
        return $this->expectHeaders([
            'Content-Type' => function (string $contentType) {
                $this->testCase::assertStringContainsString('multipart/form-data', $contentType);
            }
        ])
            ->addExpectation(function (RequestInterface $request) use ($values) {
                $body = $request->getBody()->getContents();
                foreach ($values as $value) {
                    $this->testCase::assertStringContainsString("name=\"{$value['name']}\"", $body);
                    $this->testCase::assertStringContainsString($value['contents'], $body);
                }
            });
    }

    /**
     * @param Response $response
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function respondWith(Response $response): self
    {
        return $this->setResponse($response);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param \Closure $checker
     *
     * @return self
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function addExpectation(\Closure $checker): self
    {
        $this->expectations[] = $checker;
        return $this;
    }

    /**
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function build(): FeatureTestCase
    {
        ($this->callback)(function (Request $request, array $guzzleOptions): Response {
            foreach ($this->expectations as $expectation) {
                $expectation($request, $guzzleOptions);
            }

            return $this->response;
        });

        return $this->testCase;
    }
}
