<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation\Factory;

use Waffler\Waffler\Implementation\Exceptions\NotAnInterfaceException;
use Waffler\Waffler\Implementation\Traits\BuildsImplementationFileName;
use Waffler\Waffler\Implementation\Traits\InteractsWithAttributes;

class FileCacheFactory extends AbstractFactoryDecorator
{
    use InteractsWithAttributes;
    use BuildsImplementationFileName;

    public function __construct(
        FactoryInterface $factory,
        private readonly string $cacheDirectory,
        private readonly string $baseNamespace,
    ) {
        parent::__construct($factory);
    }

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
        $code = parent::generateForInterface($interface);
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
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function buildFilepath(string $interface): string
    {
        return $this->cacheDirectory.DIRECTORY_SEPARATOR.$this->buildFileName($interface).'.php';
    }
}
