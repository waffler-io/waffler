<?php

declare(strict_types=1);

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client;

use Closure;
use Waffler\Component\Generator\ClassGenerator;
use Waffler\Component\Generator\FileClassRepository;
use Waffler\Component\Generator\MethodValidator;
use Waffler\Component\Generator\PathParser;
use Waffler\Component\HttpClient\GuzzleHttpClientWrapper;
use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Contracts\Client\HttpClientChangeableInterface;
use Waffler\Contracts\Client\PregeneratesClientsInterface;
use Waffler\Contracts\Generator\ClassGeneratorInterface;
use Waffler\Contracts\Generator\ClassRepositoryInterface;
use Waffler\Contracts\HttpClient\ClientInterface;

/**
 * Class Factory
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-import-type FactoryClosure from HttpClientChangeableInterface
 */
class Factory implements FactoryInterface, PregeneratesClientsInterface, HttpClientChangeableInterface
{
    /**
     * @phpstan-var FactoryClosure
     */
    private Closure $httpClientFactory {
        get => $this->httpClientFactory ??= $this->defaultHttpClientFactory(...);
        set => $this->httpClientFactory = $value;
    }

    /**
     * @param ClassRepositoryInterface $classRepository
     * @param ClassGeneratorInterface  $classGenerator
     */
    public function __construct(
        protected readonly ClassRepositoryInterface $classRepository,
        protected readonly ClassGeneratorInterface $classGenerator,
    ) {}

    public static function default(): self
    {
        return new self(
            new FileClassRepository(),
            new ClassGenerator(
                new MethodValidator(),
                new PathParser(),
            ),
        );
    }

    /**
     * @param Closure                $closure
     *
     * @phpstan-param FactoryClosure $closure
     *
     * @return $this
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function setHttpClientFactory(Closure $closure): static
    {
        $this->httpClientFactory = $closure;

        return $this;
    }

    public function make(string $interface, array $options = []): object
    {
        $cachedClass = $this->classRepository->has($interface)
            ? $this->classRepository->get($interface)
            : $this->classRepository->save(
                $interface,
                $this->classGenerator->generateClass($interface),
            );

        return new ($cachedClass->classFqn)($options, $this, ($this->httpClientFactory)($options));
    }

    public function warmup(string $interface): void
    {
        if (!$this->classRepository->has($interface)) {
            $this->classRepository->save(
                $interface,
                $this->classGenerator->generateClass($interface),
            );
        }
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return ClientInterface
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function defaultHttpClientFactory(array $options): ClientInterface
    {
        return new GuzzleHttpClientWrapper($options);
    }
}
