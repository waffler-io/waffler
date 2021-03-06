<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client\Readers;

use BadMethodCallException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionNamedType;
use Waffler\Waffler\Attributes\Contracts\Verb;
use Waffler\Waffler\Attributes\Request\Headers;
use Waffler\Waffler\Attributes\Request\Path;
use Waffler\Waffler\Attributes\Request\Produces;
use Waffler\Waffler\Attributes\Request\Timeout;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Utils\NestedResource;
use Waffler\Waffler\Attributes\Utils\Suppress;
use Waffler\Waffler\Attributes\Utils\Unwrap;
use Waffler\Waffler\Client\Exceptions\MethodIsNotBatchedException;
use Waffler\Waffler\Client\Traits\InteractsWithAttributes;

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
     * @param array<string>            $pathPrefix
     */
    public function __construct(
        private ReflectionMethod $reflectionMethod,
        private array $arguments,
        private array $pathPrefix = []
    ) {
    }

    public function isSuppressed(): bool
    {
        return $this->reflectionHasAttribute($this->reflectionMethod, Suppress::class);
    }

    public function isBatched(): bool
    {
        return $this->reflectionHasAttribute($this->reflectionMethod, Batch::class);
    }

    /**
     * Retrieves the batched method.
     *
     * @return ReflectionMethod
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @throws \Waffler\Waffler\Client\Exceptions\MethodIsNotBatchedException
     * @throws \ReflectionException
     */
    public function getBatchedMethod(): ReflectionMethod
    {
        if ($this->isBatched()) {
            return $this->reflectionMethod->getDeclaringClass()
                ->getMethod($this->getAttributeInstance($this->reflectionMethod, Batch::class)->methodName);
        }

        throw new MethodIsNotBatchedException($this->reflectionMethod);
    }

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
        $declaringClass = $this->reflectionMethod->getDeclaringClass();

        $path = $this->pathPrefix;

        $pathAttributes = [
            ...$this->getAttributeInstances($declaringClass, Path::class),
            ...$this->getAttributeInstances($this->reflectionMethod, Path::class)
        ];

        foreach ($pathAttributes as $pathAttribute) {
            $this->addPathParts($pathAttribute->path, $path);
        }

        if (!$this->reflectionHasAttribute($this->reflectionMethod, NestedResource::class)) {
            $this->addPathParts($this->getVerb()->getPath(), $path);
        }

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

        return $headers;
    }

    /**
     * Retrieves the verb from the reflection method.
     *
     * @return \Waffler\Waffler\Attributes\Contracts\Verb
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
        $reflectionReturnType = $this->reflectionMethod->getReturnType();

        return $reflectionReturnType instanceof ReflectionNamedType
            ? $reflectionReturnType->getName()
            : 'mixed';
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
     * @return \Waffler\Waffler\Client\Readers\ParameterReader
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
