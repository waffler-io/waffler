<?php

namespace Waffler;

/**
 * Dot notation for get data inside array
 *
 * @param array<T> $_
 * @param string   $path
 *
 * @return T
 * @psalm-suppress DuplicateFunction
 * @template       T
 */
function array_get(array $_, string $path): mixed
{
    $propNames = explode('.', $path);
    $nested = $_;
    foreach ($propNames as $propName) {
        $nested = $nested[$propName];
    }
    return $nested;
}

/**
 * Dot notation for set data inside array.
 *
 * @param array<T> $_
 * @param string   $path
 * @param T        $value
 *
 * @return void
 * @psalm-suppress DuplicateFunction
 * @template       T
 */
function array_set(array &$_, string $path, mixed $value): void
{
    $propNames = explode('.', $path);
    $paths = array_slice($propNames, 0, -1);
    $nested = &$_;
    foreach ($paths as $propName) {
        if (!is_array($nested[$propName])) {
            $nested[$propName] = [];
        }
        $nested = &$nested[$propName];
    }
    $target = array_slice($propNames, -1)[0];
    $nested[$target] = $value;
}

/**
 * Wraps a given value into an array.
 *
 * @param mixed|array<int|string, mixed> $value
 *
 * @return array<int|string, mixed>
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
function array_wrap(mixed $value): array
{
    if (is_array($value)) {
        return $value;
    }

    return [$value];
}