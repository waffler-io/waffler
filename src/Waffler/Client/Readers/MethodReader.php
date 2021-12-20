<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client\Readers;

use BadMethodCallException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\Pure;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionNamedType;
use Waffler\Attributes\Contracts\Verb;
use Waffler\Attributes\Request\Consumes;
use Waffler\Attributes\Request\Headers;
use Waffler\Attributes\Request\Path;
use Waffler\Attributes\Request\Produces;
use Waffler\Attributes\Request\Timeout;
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

    /**
     * @param \ReflectionMethod        $reflectionMethod
     * @param array<int|string, mixed> $arguments
     */
    public function __construct(
        private ReflectionMethod $reflectionMethod,
        private array $arguments
    ) {
    }

    #[Pure]
    public function isSuppressed(): bool
    {
        return $this->reflectionHasAttribute($this->reflectionMethod, Suppress::class);
    }

    #[Pure]
    public function mustUnwrap(): bool
    {
        return $this->reflectionHasAttribute($this->reflectionMethod, Unwrap::class);
    }

    public function getWrapperProperty(): string
    {
        return $this->getAttributeInstance($this->reflectionMethod, Unwrap::class)->property;
    }

    public function isAsynchronous(): bool
    {
        return is_a(
            $this->getReturnType(),
            PromiseInterface::class,
            true
        );
    }

    /**
     * @param class-string<TAttributeName> $name
     *
     * @return false|array<TAttributeName>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TAttributeName of object
     */
    private function hasAttribute(string $name): false|array
    {
        return $this->reflectionHasAttribute($this->reflectionMethod, $name)
            ? $this->getAttributeInstances($this->reflectionMethod, $name)
            : false;
    }

    private function getPath(): string
    {
        $method = $this->reflectionMethod;
        $path = [];

        if ($this->reflectionHasAttribute($method->getDeclaringClass(), Path::class)) {
            $piece = $this->getAttributeInstance($method->getDeclaringClass(), Path::class)->path;
            $this->addPathParts($piece, $path);
        }

        if ($this->hasAttribute(Path::class)) {
            $piece = $this->getAttributeInstance($method, Path::class)->path;
            $this->addPathParts($piece, $path);
        }

        $this->addPathParts($this->getVerb()->getPath(), $path);

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
     * Retrieves an array of guzzle http options.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function getOptions(): array
    {
        $parameterReader = $this->newParameterReader();

        $options = array_filter([
            RequestOptions::HEADERS => array_merge_recursive(
                $this->getHeaders(),
                $parameterReader->getHeaderParams()
            ),
            RequestOptions::BODY => $parameterReader->getBodyParam(),
            RequestOptions::JSON => $parameterReader->getJsonParams(),
            RequestOptions::QUERY => $parameterReader->getQueryParams(),
            RequestOptions::FORM_PARAMS => $parameterReader->getFormParams(),
            RequestOptions::MULTIPART => $parameterReader->getMultipartParams(),
            RequestOptions::AUTH => $parameterReader->getAuthParams(),
            RequestOptions::TIMEOUT => $this->getTimeout()
        ]);

        $options[RequestOptions::HTTP_ERRORS] = !$this->isSuppressed();

        return array_merge($options, $parameterReader->getRawOptions());
    }

    /**
     * Retrieves the request timeout.
     *
     * @return int|null
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getTimeout(): null|int
    {
        if ($timeoutList = $this->hasAttribute(Timeout::class)) {
            return $timeoutList[0]->timeout;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getHeaders(): array
    {
        $headers = [];

        if ($headersAttribute = $this->hasAttribute(Headers::class)) {
            $headers = $headersAttribute[0]->headers;
        }

        if ($producesAttribute = $this->hasAttribute(Produces::class)) {
            $headers = array_merge_recursive($headers, $producesAttribute[0]->headers);
        }

        if ($consumesAttribute = $this->hasAttribute(Consumes::class)) {
            $headers = array_merge_recursive($headers, $consumesAttribute[0]->headers);
        }

        return $headers;
    }

    /**
     * Retrieves the verb from the reflection method.
     *
     * @return \Waffler\Attributes\Contracts\Verb
     * @throws \BadMethodCallException If the reflection method has no verb attribute.
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getVerb(): Verb
    {
        $reflectionAttributes = $this->reflectionMethod->getAttributes(Verb::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($reflectionAttributes as $attribute) {
            $instance = $attribute->newInstance();
            if (is_a($instance, Verb::class)) {
                return $instance;
            }
        }
        throw new BadMethodCallException("The method {$this->reflectionMethod->getName()} has no verb attribute.");
    }

    /**
     * Loads the method return type.
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getReturnType(): string
    {
        $method = $this->reflectionMethod;
        if ($method->hasReturnType() && $method->getReturnType() instanceof ReflectionNamedType) {
            return $method->getReturnType()->getName();
        } else {
            return 'mixed';
        }
    }

    /**
     * Parse the uri path with the given arguments.
     *
     * @return string
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function parsePath(): string
    {
        return $this->newParameterReader()->parsePath($this->getPath());
    }

    /**
     * Retrieves new parameter reader instance.
     *
     * @return \Waffler\Client\Readers\ParameterReader
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function newParameterReader(): ParameterReader
    {
        return new ParameterReader(
            $this->reflectionMethod->getParameters(),
            $this->arguments
        );
    }
}
