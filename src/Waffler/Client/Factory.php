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
readonly class Factory implements FactoryInterface
{
    private const IMPL_CACHE_DIRECTORY = __DIR__.'/../../Impl';
    private const NAMESPACE = "Waffler\\Impl";

    public function __construct(
        private ImplFactory $classFactory = new FileCacheFactory(
            new ClassFactory(
                new MethodValidator(),
                new PathParser(),
                self::NAMESPACE,
            ),
            self::IMPL_CACHE_DIRECTORY,
        )
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public function make(string $interface, array $options = []): object
    {
        $className = $this->classFactory->generateForInterface($interface);

        return new $className($options, $this);
    }
}
