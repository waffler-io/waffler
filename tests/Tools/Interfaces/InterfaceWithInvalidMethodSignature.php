<?php

namespace Waffler\Tests\Tools\Interfaces;

interface InterfaceWithInvalidMethodSignature
{
    public function intersectionReturnType(): int|string;

    public function intersectionParameter(int|string $value): void;

    public function parameterPassedByReference(int &$value): void;

    public static function staticMethod(): void;

    public function variadicParameter(string ...$args): void;
}