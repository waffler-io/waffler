<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Fixtures\Interfaces;

interface InterfaceWithInvalidMethodSignature
{
    public function intersectionReturnType(): int|string;

    public function intersectionParameter(int|string $value): void;

    public function parameterPassedByReference(int &$value): void;

    public static function staticMethod(): void;

    public function variadicParameter(string ...$args): void;
}
