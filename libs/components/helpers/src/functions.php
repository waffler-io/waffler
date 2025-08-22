<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Helpers;

use JetBrains\PhpStorm\Pure;

/**
 * Dot notation for get data inside the array
 *
 * @param array<R>             $_
 * @param string|array<string> $path
 * @param non-empty-string     $pathSeparator
 *
 * @return R
 * @template R of mixed|array<R>
 */
#[Pure]
function arrayGet(array $_, string|array $path, string $pathSeparator = '.'): mixed
{
    return Arr::get($_, $path, $pathSeparator);
}

/**
 * Dot notation for set value inside the array.
 *
 * @param array<int|string, mixed>                 $_
 * @param non-empty-string|array<non-empty-string> $path
 * @param mixed                                    $value
 * @param non-empty-string                         $pathSeparator
 *
 * @return void
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
function arraySet(array &$_, string|array $path, mixed $value, string $pathSeparator = '.'): void
{
    Arr::set($_, $path, $value, $pathSeparator);
}

/**
 * Wraps a given value into an array.
 *
 * @param T $value
 *
 * @return (T is array ? T : array<T>)
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template T
 */
#[Pure]
function arrayWrap(mixed $value): array
{
    return Arr::wrap($value);
}
