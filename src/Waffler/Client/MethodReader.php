<?php

namespace Waffler\Client;

use BadMethodCallException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\Pure;
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
 * Class MethodReader.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class MethodReader
{
    use InteractsWithAttributes;

    public function __construct(
        private ParameterReader $parameterReader
    ) {
    }

    /**
     * Loads the reflection parameters and the list of real arguments into the parameter reader.
     *
     * @param array<\ReflectionParameter> $parameters
     * @param array<int|string, mixed> $arguments
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function setParameterReaderData(array $parameters, array $arguments): void
    {
        $this->parameterReader->setData($parameters, $arguments);
    }

    #[Pure]
    public function isSuppressed(ReflectionMethod $method): bool
    {
        return $this->reflectionHasAttribute($method, Suppress::class);
    }

    #[Pure]
    public function mustUnwrap(ReflectionMethod $method): bool
    {
        return $this->reflectionHasAttribute($method, Unwrap::class);
    }

    public function getWrapperProperty(ReflectionMethod $method): string
    {
        return $this->getAttributeInstance($method, Unwrap::class)->property;
    }

    public function isAsynchronous(ReflectionMethod $method): bool
    {
        return is_a($this->getReturnType($method), PromiseInterface::class, true);
    }

    /**
     * @param class-string<TAttributeName> $name
     *
     * @return false|array<TAttributeName>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TAttributeName of object
     */
    public function hasAttribute(ReflectionMethod $method, string $name): false|array
    {
        return $this->reflectionHasAttribute($method, $name)
            ? $this->getAttributeInstances($method, $name)
            : false;
    }

    public function getPath(ReflectionMethod $method): string
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

        $this->addPathParts($this->getVerb($method)->getPath(), $path);

        return join('/', $path);
    }

    /**
     * @param string        $path
     * @param array<string> $parts
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function addPathParts(string $path, array &$parts): void
    {
        foreach (explode('/', $path) as $item) {
            if (empty($item)) {
                continue;
            }
            $parts[] = $item;
        }
    }

    /**
     * Retrieves an array of guzzle http options.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function getOptions(ReflectionMethod $method): array
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
    public function getHeaders(ReflectionMethod $method): array
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
     * @return \Waffler\Attributes\Contracts\Verb
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getVerb(ReflectionMethod $method): Verb
    {
        foreach ($method->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if (is_a($instance, Verb::class)) {
                return $instance;
            }
        }
        throw new BadMethodCallException("The method {$method->getName()} has no verb attribute.");
    }

    /**
     * Loads the method return type.
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getReturnType(ReflectionMethod $method): string
    {
        if ($method->hasReturnType() && $method->getReturnType() instanceof \ReflectionNamedType) {
            return $method->getReturnType()->getName();
        } else {
            return 'mixed';
        }
    }

    public function parseFullPath(ReflectionMethod $method): string
    {
        return $this->parameterReader->parsePath($this->getPath($method));
    }
}