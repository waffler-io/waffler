<?php

declare(strict_types=1);

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
 * Class Arr.
 *
 * A collection of functions to work with arrays.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class Arr
{
    /**
     * Dot notation for get data inside the array
     *
     * @param array<int|string, R> $_
     * @param string|array<string> $path
     * @param non-empty-string     $pathSeparator
     *
     * @return R
     * @template R of mixed|array<int|string, R>
     */
    #[Pure]
    public static function get(array $_, string|array $path, string $pathSeparator = '.'): mixed
    {
        $propNames = is_array($path) ? $path : explode($pathSeparator, $path);
        $nested = $_;
        foreach ($propNames as $propName) {
            if (! is_array($nested)) {
                return $nested;
            }
            $nested = $nested[$propName];
        }
        return $nested;
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
    public static function set(array &$_, string|array $path, mixed $value, string $pathSeparator = '.'): void
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
    public static function wrap(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }
}
