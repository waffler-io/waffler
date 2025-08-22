<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Exceptions;

use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

class InterfaceMethodValidationException extends \RuntimeException implements GeneratorExceptionInterface
{
    public const METHOD_NOT_ALLOWED = 0;
    public const STATIC_METHODS_ARE_NOT_ALLOWED = 1;
    public const VERB_IS_MISSING = 2;
    public const PARAMETERS_WITHOUT_A_TYPE_ARE_NOT_ALLOWED = 3;
    public const VARIADIC_OR_REFERENCE_PARAMETERS_ARE_NOT_ALLOWED = 4;
    public const UNION_TYPES_OR_INTERSECTION_TYPES_ARE_NOT_ALLOWED = 5;
    public const PARAMETER_TYPE_NOT_COMPATIBLE_WITH_ATTRIBUTE = 6;
    public const INVALID_BATCH_METHOD_ARGUMENT = 7;
    public const INVALID_BATCH_METHOD_RETURN_TYPE = 8;
    public const BATCH_METHODS_CANNOT_CALL_ANOTHER_BATCH_METHOD = 9;
    public const INVALID_METHOD_RETURN_TYPE = 10;

    private const ERROR_MESSAGES = [
        self::METHOD_NOT_ALLOWED => "Method %s not allowed",
        self::STATIC_METHODS_ARE_NOT_ALLOWED => "Static methods are not allowed",
        self::VERB_IS_MISSING => "Verb is missing. Please fix [%s].",
        self::PARAMETERS_WITHOUT_A_TYPE_ARE_NOT_ALLOWED => "Parameters without a type are not allowed. Please fix [%s] method.",
        self::VARIADIC_OR_REFERENCE_PARAMETERS_ARE_NOT_ALLOWED => "Variadic or reference parameters are not allowed",
        self::UNION_TYPES_OR_INTERSECTION_TYPES_ARE_NOT_ALLOWED => "Union types or intersection types are not allowed",
        self::PARAMETER_TYPE_NOT_COMPATIBLE_WITH_ATTRIBUTE => "The attribute %s can annotate a parameter with one of %s types, %s given.",
        self::INVALID_BATCH_METHOD_ARGUMENT => "The method must have exactly one argument and must be of type array.",
        self::INVALID_BATCH_METHOD_RETURN_TYPE => "The method must return an array or a promise.",
        self::BATCH_METHODS_CANNOT_CALL_ANOTHER_BATCH_METHOD => 'Batch methods cannot call another batch method.',
        self::INVALID_METHOD_RETURN_TYPE => 'The return type is not allowed. Allowed types are: %s.',
    ];

    /**
     * @param int               $code
     * @param array<string|int> $messageReplacements
     */
    public function __construct(int $code, array $messageReplacements = [])
    {
        parent::__construct(
            sprintf(self::ERROR_MESSAGES[$code], ...$messageReplacements),
            $code,
        );
    }
}
