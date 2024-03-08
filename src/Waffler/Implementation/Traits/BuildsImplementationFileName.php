<?php

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
