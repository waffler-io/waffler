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

use Waffler\Waffler\Client\Contracts\FactoryInterface;
use Waffler\Waffler\Implementation\Factory\ClassFactory;
use Waffler\Waffler\Implementation\Factory\FactoryInterface as ImplFactory;
use Waffler\Waffler\Implementation\Factory\FileCacheFactory;
use Waffler\Waffler\Implementation\MethodValidator;
use Waffler\Waffler\Implementation\PathParser;

/**
 * Class Factory
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class Factory implements FactoryInterface
{
    private const string IMPL_CACHE_DIRECTORY = __DIR__.'/../../../generated';
    private const string NAMESPACE = "Waffler\\Generated";

    /**
     * WARNING: This constructor should not be called directly. It is intended for internal usage only.
     * The signature must change at any time without prior warnings.
     *
     * Use {@see Factory::default()} method instead or implement the {@see FactoryInterface} in your own class.
     *
     * @param ImplFactory $classFactory
     */
    public function __construct(protected readonly ImplFactory $classFactory)
    {
    }

    public function make(string $interface, array $options = []): object
    {
        $className = $this->classFactory->generateForInterface($interface);

        return new $className($options, $this);
    }

    public static function default(): FactoryInterface
    {
        return new self(
            new FileCacheFactory(
                new ClassFactory(
                    new MethodValidator(),
                    new PathParser(),
                    self::NAMESPACE,
                ),
                self::IMPL_CACHE_DIRECTORY,
                self::NAMESPACE,
            )
        );
    }
}
