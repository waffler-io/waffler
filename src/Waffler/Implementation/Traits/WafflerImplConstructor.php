<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation\Traits;

use GuzzleHttp\Client;
use Waffler\Waffler\Client\Contracts\FactoryInterface;
use Waffler\Waffler\Client\MakerInterface;

trait WafflerImplConstructor
{
    private Client $client;

    public function __construct(
        private readonly array $options,
        private readonly FactoryInterface $factory,
    ) {
        $this->client = new Client($this->options);
    }

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
            'base_uri' => ($this->options['base_uri'] ?? '') . ($options['base_uri'] ?? '') . '/'
        ]);
    }
}
