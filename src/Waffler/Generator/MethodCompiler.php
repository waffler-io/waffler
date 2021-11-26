<?php

declare(strict_types = 1);

namespace Waffler\Generator;

use Exception;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Stringable;

/**
 * Class MethodGenerator
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @internal For internal use only.
 */
class MethodCompiler implements Stringable
{
    protected string $representation;

    /**
     * @throws \Exception
     */
    public function __construct(protected ReflectionMethod $method)
    {
        $this->assertMethodSignature();
        $this->compile();
    }

    /**
     * Ensures the method follows certain criteria.
     *
     * @throws \Exception
     */
    protected function assertMethodSignature(): void
    {
        $finalQuote = "Please fix the method \"{$this->method->getName()}\" signature.";

        if ($this->method->isStatic() || !$this->method->isAbstract()) {
            throw new Exception(
                "Static or concrete methods are not allowed. {$finalQuote}"
            );
        }

        foreach ($this->method->getParameters() as $parameter) {
            if (($parameter->isVariadic() || $parameter->isPassedByReference())) {
                throw new Exception(
                    "Variadic or passed by reference parameters are forbidden. {$finalQuote}"
                );
            } elseif ($parameter->hasType()) {
                $reflectionType = $parameter->getType();

                if ($reflectionType instanceof ReflectionUnionType) {
                    throw new Exception("Union types are not allowed. $finalQuote");
                } elseif (PHP_VERSION_ID >= 80100 && $reflectionType instanceof ReflectionIntersectionType) {
                    throw new Exception("Intersection types are not allowed. {$finalQuote}");
                }
            }
        }
    }

    public function __toString(): string
    {
        return $this->representation;
    }

    /**
     * Compiles the method to its string representation.
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    protected function compile(): void
    {
        $parameterList = [];
        foreach ($this->method->getParameters() as $parameter) {
            $paramType = $this->getParameterType($parameter);
            $argName = $parameter->getName();
            $defaultValue = $this->getParameterDefaultValue($parameter);
            $parameterList[] = "{$paramType} \${$argName}{$defaultValue}";
        }
        $parameterList = join(',', $parameterList);
        $this->representation = "public function {$this->method->getName()}({$parameterList})";
        if (($returnTypeReflection = $this->method->getReturnType())
            && $returnTypeReflection instanceof ReflectionNamedType) {
            $returnTypeName = $this->getTypeName($returnTypeReflection);
            $returnStatement = $returnTypeName === 'void' ? '' : 'return ';
            $this->representation .= ": {$returnTypeName} { {$returnStatement}";
        } else {
            $this->representation .= '{ return';
        }
        $this->representation .= "\$this->_callHandler(\"{$this->method->getName()}\", func_get_args());}";
    }

    /**
     * Retrieves the fully qualified name of the type.
     *
     * @param \ReflectionNamedType $reflectionType
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    protected function getTypeName(ReflectionNamedType $reflectionType): string
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
    protected function getParameterType(ReflectionParameter $parameter): string
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
    protected function getParameterDefaultValue(ReflectionParameter $parameter): string
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return '';
        } elseif (is_string($defaultValue = $parameter->getDefaultValue())) {
            return "=\"{$defaultValue}\"";
        }

        return '=' . var_export($defaultValue, true);
    }
}
