<?php

declare(strict_types=1);

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client;

use GuzzleHttp\ClientInterface;
use JetBrains\PhpStorm\Pure;
use ReflectionMethod;
use Waffler\Waffler\Client\Readers\MethodReader;

/**
 * Class MethodReader
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Client
 * @internal
 */
class MethodInvoker
{
    /**
     * @param \Waffler\Waffler\Client\ResponseParser $responseParser
     * @param \GuzzleHttp\ClientInterface            $client
     */
    public function __construct(
        private ResponseParser $responseParser,
        private ClientInterface $client,
    ) {
    }

    /**
     * @param \ReflectionMethod        $method
     * @param array<int|string, mixed> $arguments
     * @param array<string>            $pathPrefix
     *
     * @return mixed
     * @throws \Exception
     */
    public function invokeMethod(ReflectionMethod $method, array $arguments, array $pathPrefix = []): mixed
    {
        $methodReader = $this->newMethodReader($method, $arguments, $pathPrefix);

        $response = $this->client->requestAsync(
            $methodReader->getVerb()->getName(),
            $methodReader->parsePath(),
            $methodReader->getOptions()
        );

        if ($methodReader->isAsynchronous()) {
            return $response;
        }

        return $this->responseParser->parse(
            $response->wait(),
            $methodReader->getReturnType(),
            $methodReader->mustUnwrap()
                ? $methodReader->getWrapperProperty()
                : null
        );
    }

    /**
     * @param \ReflectionMethod        $reflectionMethod
     * @param array<int|string, mixed> $arguments
     * @param array<string>            $pathPrefix
     *
     * @return \Waffler\Waffler\Client\Readers\MethodReader
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Pure]
    private function newMethodReader(
        ReflectionMethod $reflectionMethod,
        array $arguments,
        array $pathPrefix = []
    ): MethodReader {
        return new MethodReader($reflectionMethod, $arguments, $pathPrefix);
    }
}
