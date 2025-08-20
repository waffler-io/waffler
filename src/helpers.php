<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler;

use JetBrains\PhpStorm\Pure;

/**
 * Dot notation for get data inside the array
 *
 * @param array<R>         $_
 * @param string|array<string> $path
 * @param non-empty-string $pathSeparator
 *
 * @return R
 * @template R of mixed|array<R>
 */
#[Pure]
function arrayGet(array $_, string|array $path, string $pathSeparator = '.'): mixed
{
    $propNames = is_array($path) ? $path : explode($pathSeparator, $path);
    $nested = $_;
    foreach ($propNames as $propName) {
        $nested = $nested[$propName];
    }
    return $nested;
}

/**
 * Dot notation for set value inside the array.
 *
 * @param array                                    $_
 * @param non-empty-string|array<non-empty-string> $path
 * @param mixed                                    $value
 * @param non-empty-string                         $pathSeparator
 *
 * @return void
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
function arraySet(array &$_, string|array $path, mixed $value, string $pathSeparator = '.'): void
{
    $keyChain = is_array($path) ? $path : explode($pathSeparator, $path);
    $path = array_slice($keyChain, 0, -1);
    $nested = &$_;
    foreach ($path as $keyName) {
        if (!isset($nested[$keyName]) || !is_array($nested[$keyName])) {
            $nested[$keyName] = [];
        }
        $nested = &$nested[$keyName];
    }
    $target = array_slice($keyChain, -1)[0];
    $nested[$target] = $value;
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
    if (is_array($value)) {
        return $value;
    }

    return [$value];
}
