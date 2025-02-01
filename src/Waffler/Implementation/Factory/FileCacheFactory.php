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

use Waffler\Waffler\Implementation\Attributes\ImplHash;
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
        $qualified = $this->buildQualifiedFileName($interface);
        if ($this->validateExistingImplementation($qualified, $interface)) {
            return $qualified;
        }

        $fileName = $this->cacheDirectory.DIRECTORY_SEPARATOR.$this->buildFileName($interface).'.php';

        if (file_exists($fileName)) {
            return $qualified;
        }

        $code = parent::generateForInterface($interface);

        $classFileResource = fopen($fileName, 'w');
        fwrite($classFileResource, $code);
        fclose($classFileResource);

        include $fileName;

        return $qualified;
    }

    /**
     * @param class-string<TImpl>      $qualified
     * @param class-string<TInterface> $interface
     *
     * @return bool
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template TImpl of object
     * @template TInterface of object
     */
    private function validateExistingImplementation(string $qualified, string $interface): bool
    {
        if (!class_exists($qualified)) {
            return false;
        }

        $qualifiedReflection = new \ReflectionClass($qualified);
        $interfaceReflection = new \ReflectionClass($interface);
        $implHash = $this->getAttributeInstance($qualifiedReflection, ImplHash::class);

        $valid = $implHash->hash === md5_file($interfaceReflection->getFileName());

        if (!$valid) {
            unlink($qualifiedReflection->getFileName());
            return false;
        }

        return true;
    }
}
