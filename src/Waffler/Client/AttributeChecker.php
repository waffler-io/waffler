<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client;

use InvalidArgumentException;
use Waffler\Waffler\Attributes\Auth\Basic;
use Waffler\Waffler\Attributes\Auth\Bearer;
use Waffler\Waffler\Attributes\Auth\Digest;
use Waffler\Waffler\Attributes\Auth\Ntml;
use Waffler\Waffler\Attributes\Request\FormData;
use Waffler\Waffler\Attributes\Request\HeaderParam;
use Waffler\Waffler\Attributes\Request\Headers;
use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Request\Multipart;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Request\QueryParam;
use Waffler\Waffler\Attributes\Utils\RawOptions;

/**
 * Class AttributeChecker.
 *
 * @author   ErickJMenezes <erickmenezes.dev>
 * @internal For internal use only.
 */
class AttributeChecker
{
    /**
     * Checks if the attribute has the expected parameters.
     *
     * @param class-string<T> $attribute
     * @param mixed           $value
     *
     * @return void
     * @template T
     * @throws \InvalidArgumentException
     */
    public static function check(string $attribute, mixed $value): void
    {
        match ($attribute) {
            Basic::class, Digest::class, Ntml::class => self::authHeaders($value),
            Query::class, Json::class, Headers::class, Multipart::class, FormData::class, RawOptions::class => self::expectTypes($attribute, ['array'], $value),
            Bearer::class, PathParam::class, QueryParam::class => self::expectTypes($attribute, ['string', 'integer', 'NULL', 'float', 'double'], $value),
            HeaderParam::class => self::expectTypes($attribute, ['string', 'NULL'], $value),
            JsonParam::class => self::expectTypes($attribute, ['string', 'integer', 'NULL', 'array', 'float', 'double'], $value),
            default => null
        };
    }

    private static function authHeaders(mixed $value): void
    {
        if (is_array($value) && count($value) >= 2) {
            return;
        }
        throw new InvalidArgumentException(
            "Auth attributes must have at least 2 values: username and password."
        );
    }

    /**
     * @param class-string  $attribute
     * @param array<string> $types
     * @param mixed         $value
     *
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private static function expectTypes(string $attribute, array $types, mixed $value): void
    {
        $type = gettype($value);
        if (
            in_array($type, $types, true)
            && ($type !== 'object' || in_array($value::class, $types, true))
        ) {
            return;
        }
        throw new InvalidArgumentException(
            sprintf(
                "The attribute %s was expecting string or int, %s given.",
                $attribute,
                $type === 'object' ? $value::class : $type
            )
        );
    }
}
