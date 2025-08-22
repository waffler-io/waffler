<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Traits;

use GuzzleHttp\Promise\Utils;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;
use Waffler\Contracts\HttpClient\ClientInterface;

/**
 * Trait WafflerImplConstructor.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-require-implements WafflerImplConstructorInterface
 * @phpstan-ignore trait.unused
 */
trait WafflerImplConstructor
{
    public function __construct(
        private readonly array $options,
        private readonly FactoryInterface $factory,
        private readonly ClientInterface $client,
    ) {}

    /**
     * Builds a nested api resource.
     *
     * @param class-string<TNestedResourceInterface> $resource
     * @param array<string, mixed>                   $options
     *
     * @return object&TNestedResourceInterface
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TNestedResourceInterface of object
     */
    private function buildNestedResource(string $resource, array $options = []): object
    {
        return $this->factory->make($resource, [
            ...$this->options,
            ...$options,
            'base_uri' => ($this->options['base_uri'] ?? '') . ($options['base_uri'] ?? '') . '/',
        ]);
    }

    /**
     * Executes a batch of asynchronous operations by calling a specified hidden method with a list of arguments.
     *
     * @param string $methodName The name of the method to be called for each set of arguments.
     * @param array  $argsList   A list of argument arrays, where each array represents the arguments for one method call.
     *
     * @return array<ResponseInterface> The combined results of all asynchronous operations after execution.
     *
     * @throws InvalidArgumentException If any element in the $argsList is not an array.
     */
    private function performBatchMethod(string $methodName, array $argsList): array
    {
        $hiddenMethodName = 'wafflerImplFor' . ucfirst($methodName);
        $requests = [];
        foreach ($argsList as $args) {
            if (!is_array($args)) {
                throw new InvalidArgumentException('All arguments must be arrays.');
            }
            $requests[] = $this->{$hiddenMethodName}(
                [RequestOptions::SYNCHRONOUS => false],
                ...$args
            );
        }
        return Utils::unwrap($requests);
    }
}
