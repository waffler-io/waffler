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
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use ReflectionNamedType;
use Waffler\Waffler\Client\Exceptions\IllegalMethodBatchingException;
use Waffler\Waffler\Client\Exceptions\InvalidBatchedMethodSignatureException;
use Waffler\Waffler\Client\Readers\MethodReader;

use function Waffler\Waffler\arrayWrap;

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

        if ($methodReader->isBatched()) {
            $this->performBatchedMethodValidations($method, $methodReader);
            return $this->invokeBatchedMethod($methodReader, $methodReader->getBatchedMethod(), $arguments[0], $pathPrefix);
        }

        $promise = $this->performRequest($methodReader);

        if ($methodReader->isAsynchronous()) {
            return $promise;
        }

        return $this->parseResponse($promise->wait(), $methodReader);
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

    /**
     * @param \ReflectionMethod                            $method
     * @param \Waffler\Waffler\Client\Readers\MethodReader $methodReader
     *
     * @return void
     * @throws \ReflectionException
     * @throws \Waffler\Waffler\Client\Exceptions\InvalidBatchedMethodSignatureException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function performBatchedMethodValidations(ReflectionMethod $method, MethodReader $methodReader): void
    {
        $parameters = $method->getParameters();
        $batchedMethod = $methodReader->getBatchedMethod();
        $methodReturnType = $method->getReturnType();
        $batchedMethodReturnType = $batchedMethod->getReturnType();

        if (
            count($parameters) !== 1
            || !($parameters[0]->getType() instanceof ReflectionNamedType)
            || $parameters[0]->getType()->getName() !== 'array'
        ) {
            throw new InvalidBatchedMethodSignatureException($method, InvalidBatchedMethodSignatureException::REASON_ARGS);
        } elseif (
            // If the method does not have return type
            ! ($methodReturnType instanceof ReflectionNamedType)
            // Or if the bached method has a return type and...
            || $batchedMethodReturnType instanceof ReflectionNamedType
            && (
                // The method returns void but the batched method does not.
                (
                    $methodReturnType->getName() === 'void'
                    && $batchedMethodReturnType->getName() !== 'void'
                )
                // Or the batched method does not return void and the method does not return an array or a promise.
                || (
                    $batchedMethodReturnType->getName() !== 'void'
                    && !(
                        $methodReturnType->getName() === 'array'
                        || is_a($methodReturnType->getName(), PromiseInterface::class, true)
                    )
                )
            )
        ) {
            throw new InvalidBatchedMethodSignatureException($method, InvalidBatchedMethodSignatureException::REASON_RETURN_TYPE);
        } elseif ($this->newMethodReader($batchedMethod, [])->isBatched()) {
            throw new IllegalMethodBatchingException($batchedMethod);
        }
    }

    /**
     * @param \Waffler\Waffler\Client\Readers\MethodReader $methodReader
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function performRequest(MethodReader $methodReader): PromiseInterface
    {
        return $this->client->requestAsync(
            $methodReader->getVerb()->getName(),
            $methodReader->parsePath(),
            $methodReader->getOptions()
        );
    }

    /**
     * @throws \Exception
     */
    private function invokeBatchedMethod(MethodReader $parentMethodReader, ReflectionMethod $method, array $argumentsList, array $pathPrefix = []): mixed
    {
        $readers = array_map(fn ($args) => $this->newMethodReader($method, arrayWrap($args), $pathPrefix), $argumentsList);

        // Creates a new promise that will perform all requests.
        $batchPromise = new Promise(function () use ($argumentsList, $readers, &$batchPromise) {
            $batch = Pool::batch(
                $this->client,
                array_map(function (MethodReader $reader) {
                    return fn () => $this->performRequest($reader);
                }, $readers),
                ['concurrency' => count($argumentsList)]
            );
            $batchPromise->resolve($batch);
        });

        if ($parentMethodReader->isAsynchronous() && $readers[0]->isAsynchronous()) {
            // If the original and the batched method are asynchronous, then this promise will be returned.
            return $batchPromise;
        } elseif ($readers[0]->getReturnType() === 'void' && $parentMethodReader->getReturnType() === 'void') {
            // If the original method returns void, and the batched method does to, null will be returned.
            // Otherwise, if the batched method returns an array, then we will return an empty array since no results
            // will be returned from the original method.
            $batchPromise->wait();
            return $parentMethodReader->getReturnType() === 'array' ? [] : null;
        }

        $mappedResultPromise = $batchPromise->then(
            fn (array $responses): array => array_map(
                fn (ResponseInterface $response, int $index): mixed => $this->parseResponse($response, $readers[$index]),
                $responses,
                array_keys($responses)
            )
        );

        if ($parentMethodReader->isAsynchronous()) {
            return $mappedResultPromise;
        }

        return $mappedResultPromise->wait();
    }

    private function parseResponse(ResponseInterface $response, MethodReader $methodReader): mixed
    {
        return $this->responseParser->parse(
            $response,
            $methodReader->getReturnType(),
            $methodReader->mustUnwrap()
                ? $methodReader->getWrapperProperty()
                : null
        );
    }
}
