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

/**
 * Dot notation for get data inside array
 *
 * @param array<T> $_
 * @param string   $path
 *
 * @return T
 * @template T
 */
function arrayGet(array $_, string $path): mixed
{
    $propNames = explode('.', $path);
    $nested = $_;
    foreach ($propNames as $propName) {
        $nested = $nested[$propName];
    }
    return $nested;
}

/**
 * Wraps a given value into an array.
 *
 * @param mixed|array<int|string, mixed> $value
 *
 * @return array<int|string, mixed>
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
function arrayWrap(mixed $value): array
{
    if (is_array($value)) {
        return $value;
    }

    return [$value];
}
