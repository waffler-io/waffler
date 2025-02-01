<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation\Traits;

trait BuildsImplementationFileName
{
    private function buildFileName(string $interface): string
    {
        return str_replace('\\', '_', $interface).'Impl';
    }

    private function buildQualifiedFileName(string $interface): string
    {
        return "\\Waffler\\Impl\\".$this->buildFileName($interface);
    }
}
