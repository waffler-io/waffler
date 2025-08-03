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
    ) {
        parent::__construct($factory);
    }

    /**
     * {@inheritDoc}
     *
     * @param class-string<TInterface> $interface
     *
     * @return class-string<TInterface&\Waffler\Waffler\Implementation\Traits\WafflerImplConstructor>
     * @template TInterface of object
     */
    public function generateForInterface(string $interface): string
    {
        if (! interface_exists($interface)) {
            throw new NotAnInterfaceException($interface);
        }
        $qualified = $this->buildQualifiedFileName($interface);
        $fileName = $this->cacheDirectory.DIRECTORY_SEPARATOR.$this->buildFileName($interface).'.php';
        if (file_exists($fileName)) {
            include_once $fileName;
            return $qualified;
        }
        $code = parent::generateForInterface($interface);
        $classFileResource = fopen($fileName, 'w');
        fwrite($classFileResource, $code);
        fclose($classFileResource);
        include_once $fileName;

        return $qualified;
    }
}
