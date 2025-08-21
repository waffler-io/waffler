<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Factory;

use ReflectionException;
use Waffler\Component\Generator\Exceptions\NotAnInterfaceException;
use Waffler\Component\Generator\Traits\BuildsImplementationFileName;
use Waffler\Component\Generator\Traits\InteractsWithAttributes;

class FileCacheFactory implements FactoryInterface
{
    use InteractsWithAttributes;
    use BuildsImplementationFileName;

    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly string $cacheDirectory = FactoryDefaults::IMPL_CACHE_DIRECTORY,
        private readonly string $baseNamespace = FactoryDefaults::NAMESPACE,
    ) {}

    public function generateForInterface(string $interface): string
    {
        if (! interface_exists($interface)) {
            throw new NotAnInterfaceException($interface);
        }
        $qualified = $this->buildQualifiedFileName($interface);
        $filepath = $this->buildFilepath($interface);
        if (file_exists($filepath)) {
            return $qualified;
        }
        $code = $this->factory->generateForInterface($interface);
        $this->saveClassCodeIntoCache($filepath, $code);

        return $qualified;
    }

    private function getBaseNamespace(): string
    {
        return $this->baseNamespace;
    }

    private function saveClassCodeIntoCache(string $filepath, string $code): void
    {
        file_put_contents($filepath, $code);
    }

    /**
     * @param string $interface
     *
     * @return string
     * @throws ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function buildFilepath(string $interface): string
    {
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . $this->buildFileName($interface) . '.php';
    }
}
