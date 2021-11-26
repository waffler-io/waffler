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
 * Class Method
 *
 * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package        Waffler\Client
 * @template       TInterfaceType of object
 * @internal
 */
class Method
{
    /**
     * @template-use \Waffler\Client\Traits\InteractsWithAttributes<TInterfaceType>
     */
    use InteractsWithAttributes;

    private Verb $verb;

    private string $returnType;

    /**
     * @var \Waffler\Client\Parameters<TInterfaceType>
     */
    private Parameters $parameters;

    /**
     * @param \ReflectionMethod           $method
     * @param array<int|string, mixed>    $arguments
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(
        private ReflectionMethod $method,
        array $arguments,
        private ClientInterface $client
    ) {
        $this->parameters = new Parameters($method->getParameters(), $arguments);
        if ($this->method->hasReturnType() && $this->method->getReturnType() instanceof \ReflectionNamedType) {
            $this->returnType = $this->method->getReturnType()->getName();
        } else {
            $this->returnType = 'mixed';
        }
        $this->loadVerb();
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function call(): mixed
    {
        $response = $this->client->requestAsync(
            $this->verb->getName(),
            $this->parameters->parsePath($this->getPath()),
            $this->getOptions()
        );

        if ($this->isAsynchronous()) {
            return $response;
        }

        return $this->parseResponse($response->wait());
    }

    /**
     * @return string|class-string
     */
    public function getReturnType(): string
    {
        return $this->returnType;
    }

    #[Pure]
    public function isSuppressed(): bool
    {
        return $this->reflectionHasAttribute($this->method, Suppress::class);
    }

    #[Pure]
    public function mustUnwrap(): bool
    {
        return $this->reflectionHasAttribute($this->method, Unwrap::class);
    }

    public function getWrapperProperty(): string
    {
        return $this->getAttributeInstance($this->method, Unwrap::class)->property;
    }

    public function isAsynchronous(): bool
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
    public function hasAttribute(string $name): false|array
    {
        return $this->reflectionHasAttribute($this->method, $name)
            ? $this->getAttributeInstances($this->method, $name)
            : false;
    }

    public function getPath(): string
    {
        $path = [];

        if ($this->reflectionHasAttribute($this->method->getDeclaringClass(), Path::class)) {
            $piece = $this->getAttributeInstance($this->method->getDeclaringClass(), Path::class)->path;
            $this->addPathParts($piece, $path);
        }

        if ($this->hasAttribute(Path::class)) {
            $piece = $this->getAttributeInstance($this->method, Path::class)->path;
            $this->addPathParts($piece, $path);
        }

        $this->addPathParts($this->verb->getPath(), $path);

        return join('/', $path);
    }

    // private

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
     *
     * @return mixed
     * @throws \TypeError|\Exception If the response type is not recognized.
     */
    private function parseResponse(ResponseInterface $response): mixed
    {
        return $this->getResponseParser()->parse($response, $this);
    }

    /**
     * @return array<string, mixed>
     * @throws \Exception
     */
    private function getOptions(): array
    {
        $options = array_filter([
            RequestOptions::HEADERS => array_merge_recursive($this->getHeaders(), $this->parameters->getHeaderParams()),
            RequestOptions::BODY => $this->parameters->getBodyParam(),
            RequestOptions::JSON => $this->parameters->getJsonParams(),
            RequestOptions::QUERY => $this->parameters->getQueryParams(),
            RequestOptions::FORM_PARAMS => $this->parameters->getFormParams(),
            RequestOptions::MULTIPART => $this->parameters->getMultipartParams(),
            RequestOptions::AUTH => $this->parameters->getAuthParams()
        ]);

        $options[RequestOptions::HTTP_ERRORS] = !$this->isSuppressed();

        return array_merge($options, $this->parameters->getRawOptions());
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

    private function loadVerb(): void
    {
        foreach ($this->method->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if (is_a($instance, Verb::class)) {
                $this->verb = $instance;
                return;
            }
        }
        throw new BadMethodCallException("The method {$this->method->getName()} has no verb attribute.");
    }

    /**
     * @returns \Waffler\Client\ResponseParser
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Pure]
    private function getResponseParser(): ResponseParser
    {
        return new ResponseParser();
    }
}
