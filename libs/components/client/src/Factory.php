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
use Waffler\Component\Generator\Factory\ClientClassFactory;
use Waffler\Component\Generator\Contracts\ClientClassFactoryInterface as ImplFactory;
use Waffler\Component\Generator\Factory\FileCacheFactory;
use Waffler\Component\Generator\MethodValidator;
use Waffler\Component\Generator\PathParser;
use Waffler\Component\HttpClient\GuzzleHttpClientWrapper;
use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Contracts\Client\HttpClientChangeableInterface;
use Waffler\Contracts\Client\PregeneratesClientsInterface;

/**
 * Class Factory
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-import-type FactoryClosure from HttpClientChangeableInterface
 */
class Factory implements FactoryInterface, PregeneratesClientsInterface, HttpClientChangeableInterface
{
    private ?Closure $httpClientFactory = null;

    /**
     * WARNING: This constructor should not be called directly. It is intended for internal usage only.
     * The signature must change at any time without prior warnings.
     *
     * Use {@see Factory::default()} method instead or implement the {@see FactoryInterface} in your own class.
     *
     * @param ImplFactory $classFactory
     */
    public function __construct(protected readonly ImplFactory $classFactory) {}

    public static function default(): self
    {
        return new self(
            new FileCacheFactory(
                new ClientClassFactory(
                    new MethodValidator(),
                    new PathParser(),
                ),
            ),
        );
    }

    /**
     * @param Closure $closure
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
        $className = $this->classFactory->generateForInterface($interface);
        $httpClientFactory = $this->getHttpClientFactory();

        return new $className($options, $this, $httpClientFactory($options));
    }

    public function warmup(string $interface): void
    {
        $this->classFactory->generateForInterface($interface);
    }

    /**
     * @return Closure
     * @phpstan-return FactoryClosure $closure
     */
    private function getHttpClientFactory(): Closure
    {
        return $this->httpClientFactory ??= static fn(array $options) => new GuzzleHttpClientWrapper($options);
    }
}
