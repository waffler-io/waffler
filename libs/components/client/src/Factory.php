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

use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Contracts\Client\PregeneratesClientsInterface;
use Waffler\Component\Generator\Factory\ClassFactory;
use Waffler\Component\Generator\Factory\FactoryInterface as ImplFactory;
use Waffler\Component\Generator\Factory\FileCacheFactory;
use Waffler\Component\Generator\MethodValidator;
use Waffler\Component\Generator\PathParser;

/**
 * Class Factory
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class Factory implements FactoryInterface, PregeneratesClientsInterface
{
    /**
     * WARNING: This constructor should not be called directly. It is intended for internal usage only.
     * The signature must change at any time without prior warnings.
     *
     * Use {@see Factory::default()} method instead or implement the {@see FactoryInterface} in your own class.
     *
     * @param ImplFactory $classFactory
     */
    public function __construct(protected readonly ImplFactory $classFactory) {}

    public function make(string $interface, array $options = []): object
    {
        $className = $this->classFactory->generateForInterface($interface);

        return new $className($options, $this);
    }

    public function warmup(string $interface): void
    {
        $this->classFactory->generateForInterface($interface);
    }

    public static function default(): self
    {
        return new self(
            new FileCacheFactory(
                new ClassFactory(
                    new MethodValidator(),
                    new PathParser(),
                ),
            ),
        );
    }
}
