<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Traits;

use ReflectionClass;

trait BuildsImplementationFileName
{
    /**
     * @throws \ReflectionException
     */
    private function buildFileName(string $interface): string
    {
        $reflectionInterface = new ReflectionClass($interface);
        return str_replace('\\', '_', $interface)
            . md5_file($reflectionInterface->getFileName())
            . 'Impl';
    }

    /**
     * @throws \ReflectionException
     */
    private function buildQualifiedFileName(string $interface): string
    {
        return "{$this->getBaseNamespace()}\\{$this->buildFileName($interface)}";
    }

    abstract private function getBaseNamespace(): string;
}
