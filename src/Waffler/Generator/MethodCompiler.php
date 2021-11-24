<?php

declare(strict_types = 1);

namespace Waffler\Generator;

use Exception;
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
        $this->method->isStatic()
        && throw new Exception(
            "Static methods are not allowed, please remove the method \"{$this->method->getName()}\"."
        );

        foreach ($this->method->getParameters() as $parameter) {
            ($parameter->isVariadic() || $parameter->isPassedByReference())
            && throw new Exception(
                "Variadic or passed by reference parameters are forbidden. Please fix the method \"{$this->method->getName()}\"."
            );

            $parameter->hasType() && $parameter->getType() instanceof ReflectionUnionType
            && throw new Exception("Union types are not allowed.");
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
     * @psalm-suppress ArgumentTypeCoercion, PossiblyNullArgument
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
        if ($this->method->hasReturnType()) {
            $returnTypeName = $this->getTypeName($this->method->getReturnType());
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
     * @psalm-suppress MixedAssignment
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
