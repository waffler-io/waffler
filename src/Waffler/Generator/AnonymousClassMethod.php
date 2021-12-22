<?php

declare(strict_types=1);

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Generator;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use Stringable;
use Waffler\Generator\Exceptions\MethodCompilingException;

/**
 * Class AnonymousClassMethod
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @internal For internal use only.
 */
class AnonymousClassMethod implements Stringable
{
    /**
     * Generic method template.
     */
    private const METHOD_TEMPLATE = '
        public function <method>(<args>)<rt>
        {
            <return> $this->handler->{"<method>"}(...func_get_args());
        }
    ';

    /**
     * @throws MethodCompilingException|\Exception
     */
    public function __construct(protected ReflectionMethod $method)
    {
        $this->assertMethodSignature();
    }

    public function __toString(): string
    {
        $methodName = $this->method->getName();
        $argList = $this->getParameterList();
        [$returnType, $returnStatement] = $this->getReturnTypeAndReturnStatement();
        return (string)preg_replace_callback(
            '/<(\w*)>/',
            fn (array $matches) => match ($matches[1]) {
                'method' => $methodName,
                'args' => $argList,
                'rt' => $returnType,
                default => $returnStatement
            },
            self::METHOD_TEMPLATE
        );
    }

    /**
     * Ensures the method follows certain criteria.
     *
     * @throws MethodCompilingException|\Exception
     */
    private function assertMethodSignature(): void
    {
        $finalQuote = "Please fix the method \"{$this->method->getName()}\" signature.";

        if ($this->method->isStatic() || !$this->method->isAbstract()) {
            throw new MethodCompilingException(
                "Static or concrete methods are not allowed. {$finalQuote}",
                1
            );
        }

        foreach ($this->method->getParameters() as $parameter) {
            if (($parameter->isVariadic() || $parameter->isPassedByReference())) {
                throw new MethodCompilingException(
                    "Variadic or passed by reference parameters are forbidden. {$finalQuote}",
                    2
                );
            } elseif ($parameter->hasType()) {
                $this->checkReflectionType($parameter->getType(), $finalQuote);
            }
        }

        if ($this->method->hasReturnType()) {
            $this->checkReflectionType($this->method->getReturnType(), $finalQuote);
        }
    }

    /**
     * Retrieves the fully qualified name of the type.
     *
     * @param \ReflectionNamedType $reflectionType
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getTypeName(ReflectionNamedType $reflectionType): string
    {
        $name = $reflectionType->getName();
        return class_exists($name) || interface_exists($name) ? "\\{$name}" : $name;
    }

    /**
     * Retrieves the parameter type name.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getParameterType(ReflectionParameter $parameter): string
    {
        $name = '';
        if ($parameter->hasType()) {
            /** @var ReflectionNamedType $parameterType */
            $parameterType = $parameter->getType();
            $name = $this->getTypeName($parameterType);
        }
        return $name;
    }

    /**
     * Retrieves the representation of the parameter default value.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string
     * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getParameterDefaultValue(ReflectionParameter $parameter): string
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return '';
        } elseif (is_string($defaultValue = $parameter->getDefaultValue())) {
            return "=\"{$defaultValue}\"";
        }

        return '=' . var_export($defaultValue, true);
    }

    /**
     * Retrieves the list of parameters from the reflection method.
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getParameterList(): string
    {
        $parameterList = [];
        foreach ($this->method->getParameters() as $parameter) {
            $paramType = $this->getParameterType($parameter);
            $argName = $parameter->getName();
            $defaultValue = $this->getParameterDefaultValue($parameter);
            $allowsNull = $parameter->allowsNull() ? '?' : '';
            $parameterList[] = "{$allowsNull}{$paramType} \${$argName}{$defaultValue}";
        }
        return join(',', $parameterList);
    }

    /**
     * Retrieves the return type and the return statement from the reflection method.
     *
     * @return string[]
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getReturnTypeAndReturnStatement(): array
    {
        $returnTypeReflection = $this->method->getReturnType();

        if ($returnTypeReflection instanceof ReflectionNamedType) {
            $returnTypeName = $this->getTypeName($returnTypeReflection);
            $returnStatement = $returnTypeName === 'void' ? '' : 'return';

            return [": $returnTypeName", $returnStatement];
        }

        return ['', 'return'];
    }

    /**
     * @throws \Exception
     */
    private function checkReflectionType(?ReflectionType $reflectionType, string $errorQuote = ''): void
    {
        if (!$reflectionType instanceof ReflectionNamedType) {
            throw new MethodCompilingException(
                "Union types or intersection types are not allowed. $errorQuote",
                3
            );
        }
    }
}
