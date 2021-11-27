<?php

declare(strict_types = 1);

namespace Waffler\Client;

use BadMethodCallException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use Waffler\Attributes\Contracts\Verb;
use Waffler\Attributes\Request\Consumes;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Utils\Suppress;
use Waffler\Attributes\Utils\Unwrap;
use Waffler\Client\Traits\InteractsWithAttributes;

/**
 * Class MethodReader
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Client
 * @internal
 */
class MethodInvoker
{
    use InteractsWithAttributes;

    private Verb $verb;

    private string $returnType;

    /**
     * @param \Waffler\Client\ParameterReader $parameterReader
     * @param \Waffler\Client\ResponseParser  $responseParser
     * @param \GuzzleHttp\ClientInterface     $client
     */
    public function __construct(
        private ParameterReader $parameterReader,
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
    public function invoke(ReflectionMethod $method, array $arguments): mixed
    {
        $this->parameterReader->setData($method->getParameters(), $arguments);
        $this->loadReturnType($method);
        $this->loadVerb($method);

        $response = $this->client->requestAsync(
            $this->verb->getName(),
            $this->parameterReader->parsePath($this->getPath($method)),
            $this->getOptions($method)
        );

        if ($this->isAsynchronous()) {
            return $response;
        }

        return $this->parseResponse(
            $response->wait(),
            $this->mustUnwrap($method) ? $this->getWrapperProperty($method) : null
        );
    }

    #[Pure]
    private function isSuppressed(ReflectionMethod $method): bool
    {
        return $this->reflectionHasAttribute($method, Suppress::class);
    }

    #[Pure]
    private function mustUnwrap(ReflectionMethod $method): bool
    {
        return $this->reflectionHasAttribute($method, Unwrap::class);
    }

    private function getWrapperProperty(ReflectionMethod $method): string
    {
        return $this->getAttributeInstance($method, Unwrap::class)->property;
    }

    private function isAsynchronous(): bool
    {
        return is_a($this->returnType, PromiseInterface::class, true);
    }

    /**
     * @param class-string<TAttributeName> $name
     *
     * @return false|array<TAttributeName>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TAttributeName of object
     */
    private function hasAttribute(ReflectionMethod $method, string $name): false|array
    {
        return $this->reflectionHasAttribute($method, $name)
            ? $this->getAttributeInstances($method, $name)
            : false;
    }

    private function getPath(ReflectionMethod $method): string
    {
        $path = [];

        if ($this->reflectionHasAttribute($method->getDeclaringClass(), Path::class)) {
            $piece = $this->getAttributeInstance($method->getDeclaringClass(), Path::class)->path;
            $this->addPathParts($piece, $path);
        }

        if ($this->hasAttribute($method, Path::class)) {
            $piece = $this->getAttributeInstance($method, Path::class)->path;
            $this->addPathParts($piece, $path);
        }

        $this->addPathParts($this->verb->getPath(), $path);

        return join('/', $path);
    }

    /**
     * @param string        $path
     * @param array<string> $parts
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function addPathParts(string $path, array &$parts): void
    {
        foreach (explode('/', $path) as $item) {
            if (empty($item)) {
                continue;
            }
            $parts[] = $item;
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string|null                         $wrapperProperty
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function parseResponse(ResponseInterface $response, ?string $wrapperProperty): mixed
    {
        return $this->responseParser->parse(
            $response,
            $this->returnType,
            $wrapperProperty
        );
    }

    /**
     * @return array<string, mixed>
     * @throws \Exception
     */
    private function getOptions(ReflectionMethod $method): array
    {
        $options = array_filter([
            RequestOptions::HEADERS => array_merge_recursive(
                $this->getHeaders($method),
                $this->parameterReader->getHeaderParams()
            ),
            RequestOptions::BODY => $this->parameterReader->getBodyParam(),
            RequestOptions::JSON => $this->parameterReader->getJsonParams(),
            RequestOptions::QUERY => $this->parameterReader->getQueryParams(),
            RequestOptions::FORM_PARAMS => $this->parameterReader->getFormParams(),
            RequestOptions::MULTIPART => $this->parameterReader->getMultipartParams(),
            RequestOptions::AUTH => $this->parameterReader->getAuthParams()
        ]);

        $options[RequestOptions::HTTP_ERRORS] = !$this->isSuppressed($method);

        return array_merge($options, $this->parameterReader->getRawOptions());
    }

    /**
     * @return array<string, mixed>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getHeaders(ReflectionMethod $method): array
    {
        $headers = [];

        if ($headersAttribute = $this->hasAttribute($method, Headers::class)) {
            $headers = $headersAttribute[0]->headers;
        }

        if ($producesAttribute = $this->hasAttribute($method, Produces::class)) {
            $headers = array_merge_recursive($headers, $producesAttribute[0]->headers);
        }

        if ($consumesAttribute = $this->hasAttribute($method, Consumes::class)) {
            $headers = array_merge_recursive($headers, $consumesAttribute[0]->headers);
        }

        return $headers;
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function loadVerb(ReflectionMethod $method): void
    {
        foreach ($method->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if (is_a($instance, Verb::class)) {
                $this->verb = $instance;
                return;
            }
        }
        throw new BadMethodCallException("The method {$method->getName()} has no verb attribute.");
    }

    /**
     * Loads the method return type.
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function loadReturnType(ReflectionMethod $method): void
    {
        if ($method->hasReturnType() && $method->getReturnType() instanceof \ReflectionNamedType) {
            $this->returnType = $method->getReturnType()->getName();
        } else {
            $this->returnType = 'mixed';
        }
    }
}
