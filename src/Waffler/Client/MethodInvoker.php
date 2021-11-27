<?php

declare(strict_types = 1);

namespace Waffler\Client;

use GuzzleHttp\ClientInterface;
use ReflectionMethod;

/**
 * Class MethodReader
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Client
 * @internal
 */
class MethodInvoker
{
    /**
     * @param \Waffler\Client\MethodReader   $methodReader
     * @param \Waffler\Client\ResponseParser $responseParser
     * @param \GuzzleHttp\ClientInterface    $client
     */
    public function __construct(
        private MethodReader $methodReader,
        private ResponseParser $responseParser,
        private ClientInterface $client,
    ) {
    }

    /**
     * @param \ReflectionMethod        $method
     * @param array<int|string, mixed> $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function invokeMethod(ReflectionMethod $method, array $arguments): mixed
    {
        $this->methodReader->setParameterReaderData($method->getParameters(), $arguments);

        $response = $this->client->requestAsync(
            $this->methodReader->getVerb($method)->getName(),
            $this->methodReader->parseFullPath($method),
            $this->methodReader->getOptions($method)
        );

        if ($this->methodReader->isAsynchronous($method)) {
            return $response;
        }

        return $this->responseParser->parse(
            $response->wait(),
            $this->methodReader->getReturnType($method),
            $this->methodReader->mustUnwrap($method)
                ? $this->methodReader->getWrapperProperty($method)
                : null
        );
    }
}
