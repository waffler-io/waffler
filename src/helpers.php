<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler;

use JetBrains\PhpStorm\Pure;

/**
 * Dot notation for get data inside array
 *
 * @param array<string, T>     $_
 * @param string|array<string> $path
 * @param non-empty-string     $pathSeparator
 *
 * @return T
 * @template       T
 * @psalm-suppress DuplicateFunction
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
 * Dot notation for set value inside array.
 *
 * @param array<string, mixed> $_
 * @param string|array<string> $path
 * @param mixed                $value
 * @param non-empty-string     $pathSeparator
 *
 * @return void
 * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
 * @psalm-suppress DuplicateFunction
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
 * @param T|array<T> $value
 *
 * @return array<T>
 * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template       T
 * @psalm-suppress DuplicateFunction
 */
#[Pure]
function arrayWrap(mixed $value): array
{
    if (is_array($value)) {
        return $value;
    }

    return [$value];
}
