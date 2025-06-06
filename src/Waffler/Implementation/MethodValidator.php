<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation;

use ArrayObject;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use Waffler\Waffler\Attributes\Auth\Basic;
use Waffler\Waffler\Attributes\Auth\Bearer;
use Waffler\Waffler\Attributes\Auth\Digest;
use Waffler\Waffler\Attributes\Auth\Ntml;
use Waffler\Waffler\Attributes\Contracts\Verb;
use Waffler\Waffler\Attributes\Request\FormData;
use Waffler\Waffler\Attributes\Request\HeaderParam;
use Waffler\Waffler\Attributes\Request\Headers;
use Waffler\Waffler\Attributes\Request\Json;
use Waffler\Waffler\Attributes\Request\JsonParam;
use Waffler\Waffler\Attributes\Request\Multipart;
use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Request\Query;
use Waffler\Waffler\Attributes\Request\QueryParam;
use Waffler\Waffler\Attributes\Utils\Batch;
use Waffler\Waffler\Attributes\Utils\NestedResource;
use Waffler\Waffler\Attributes\Utils\RawOptions;
use Waffler\Waffler\Implementation\Exceptions\InterfaceMethodValidationException;
use Waffler\Waffler\Implementation\Traits\InteractsWithAttributes;

class MethodValidator
{
    use InteractsWithAttributes;

    /**
     * @var array<string>
     */
    private array $validatedDeclaringClasses = [];

    private const DISALLOWED_METHODS = [
        '__construct',
        '__destruct',
        '__get',
        '__set',
        '__call',
        '__callStatic',
        '__sleep',
        '__wakeup',
        '__toString',
        '__invoke',
        '__set_state',
        '__clone',
        '__debugInfo',
    ];

    /**
     * @param array<ReflectionMethod> $methods
     *
     * @return void
     * @throws \Exception
     * @throws InterfaceMethodValidationException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function validateAll(array $methods): void
    {
        foreach ($methods as $method) {
            $this->validatedDeclaringClasses[] = $method->getDeclaringClass()->getName();
            $this->validateMethodSignature($method);
        }
        $this->validatedDeclaringClasses = [];
    }

    /**
     * Ensures the method follows certain criteria.
     *
     * @throws InterfaceMethodValidationException|\Exception
     */
    private function validateMethodSignature(ReflectionMethod $method): void
    {
        if (in_array($method->getName(), self::DISALLOWED_METHODS, true)) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::METHOD_NOT_ALLOWED,
                [$method->getName()],
            );
        }

        if (
            !$this->reflectionHasAttribute($method, Verb::class, true) &&
            !$this->reflectionHasAttribute($method, NestedResource::class) &&
            !$this->reflectionHasAttribute($method, Batch::class)
        ) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::VERB_IS_MISSING,
                [$method->getDeclaringClass()->getShortName().'::'.$method->getName()]
            );
        }

        if ($method->isStatic()) {
            throw new InterfaceMethodValidationException(InterfaceMethodValidationException::STATIC_METHODS_ARE_NOT_ALLOWED);
        }

        $this->validateParameters($method);

        if ($this->reflectionHasAttribute($method, Batch::class)) {
            $batchAttributeInstance = $this->getAttributeInstance($method, Batch::class);
            $this->performBatchedMethodValidations(
                $method,
                $method->getDeclaringClass()
                    ->getMethod($batchAttributeInstance->methodName),
            );
        }

        if ($method->hasReturnType() && !$this->alreadyValidated($method->getDeclaringClass()->getName())) {
            $this->validateReturnType($method->getReturnType());
        }
    }

    private function alreadyValidated(string $declaringClass): bool
    {
        return in_array($declaringClass, $this->validatedDeclaringClasses, true);

    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return void
     * @throws \Exception
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function validateParameters(ReflectionMethod $method): void
    {
        foreach ($method->getParameters() as $parameter) {
            if (!$parameter->hasType()) {
                throw new InterfaceMethodValidationException(
                    InterfaceMethodValidationException::PARAMETERS_WITHOUT_A_TYPE_ARE_NOT_ALLOWED,
                    [$method->getDeclaringClass()->getShortName() . '::' . $method->getName()]
                );
            }

            $this->checkReflectionType($parameter->getType());

            if ($parameter->isVariadic() || $parameter->isPassedByReference()) {
                throw new InterfaceMethodValidationException(
                    InterfaceMethodValidationException::VARIADIC_OR_REFERENCE_PARAMETERS_ARE_NOT_ALLOWED,
                );
            }

            if ($attributes = $parameter->getAttributes()) {
                foreach ($attributes as $attribute) {
                    $this->checkParameterAttributeType(
                        $attribute->getName(),
                        $parameter->getType()
                            ->getName(),
                    );
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function checkReflectionType(?ReflectionType $reflectionType): void
    {
        if (!$reflectionType instanceof ReflectionNamedType) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::UNION_TYPES_OR_INTERSECTION_TYPES_ARE_NOT_ALLOWED,
            );
        }
    }

    /**
     * Checks if the attribute has the expected parameters.
     *
     * @param class-string<T> $attribute
     * @param string          $type
     *
     * @return void
     * @template T
     * @throws \InvalidArgumentException
     */
    private function checkParameterAttributeType(string $attribute, string $type): void
    {
        match ($attribute) {
            Basic::class, Digest::class, Ntml::class,
            Query::class, Json::class, Headers::class,
            Multipart::class, FormData::class, RawOptions::class => $this->expectTypes(
                $attribute,
                ['array'],
                $type,
            ),
            Bearer::class, PathParam::class => $this->expectTypes(
                $attribute,
                ['string', 'int', 'null', 'float', 'double'],
                $type,
            ),
            QueryParam::class => $this->expectTypes(
                $attribute,
                ['string', 'int', 'null', 'float', 'double', 'array'],
                $type,
            ),
            HeaderParam::class => $this->expectTypes(
                $attribute,
                ['string', 'null'],
                $type,
            ),
            JsonParam::class => $this->expectTypes(
                $attribute,
                ['string', 'int', 'null', 'array', 'float', 'double'],
                $type,
            ),
            default => null
        };
    }

    /**
     * @param class-string<T> $attribute
     * @param array<string>   $types
     * @param string          $type
     *
     * @return void
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T
     */
    private function expectTypes(string $attribute, array $types, string $type): void
    {
        if (in_array($type, $types, true)) {
            return;
        }
        $expectedTypes = implode('|', $types);
        throw new InterfaceMethodValidationException(
            InterfaceMethodValidationException::PARAMETER_TYPE_NOT_COMPATIBLE_WITH_ATTRIBUTE,
            [$attribute, $expectedTypes, $type],
        );
    }

    private function performBatchedMethodValidations(ReflectionMethod $method, ReflectionMethod $batchedMethod): void
    {
        $parameters = $method->getParameters();
        $methodReturnType = $method->getReturnType();
        $batchedMethodReturnType = $batchedMethod->getReturnType();

        if (
            count($parameters) !== 1
            || !($parameters[0]->getType() instanceof ReflectionNamedType)
            || $parameters[0]->getType()
                ->getName() !== 'array'
        ) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::INVALID_BATCH_METHOD_ARGUMENT,
            );
        } elseif (
            // If the method does not have a return type
            !($methodReturnType instanceof ReflectionNamedType)
            // Or if the bached method has a return type and...
            || $batchedMethodReturnType instanceof ReflectionNamedType
            && (
                // The method returns void but the batched method does not.
                (
                    $methodReturnType->getName() === 'void'
                    && $batchedMethodReturnType->getName() !== 'void'
                )
                // Or the batched method does not return void, and the method does not return an array or a promise.
                || (
                    $batchedMethodReturnType->getName() !== 'void'
                    && !(
                        $methodReturnType->getName() === 'array'
                        || is_a($methodReturnType->getName(), PromiseInterface::class, true)
                    )
                )
            )
        ) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::INVALID_BATCH_METHOD_RETURN_TYPE,
            );
        } elseif ($this->reflectionHasAttribute($batchedMethod, Batch::class)) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::BATCH_METHODS_CANNOT_CALL_ANOTHER_BATCH_METHOD,
            );
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function validateReturnType(ReflectionType $type): void
    {
        $this->checkReflectionType($type);

        /** @type ReflectionNamedType $type */
        if (interface_exists($type->getName())) {
            $this->validateAll((new \ReflectionClass($type->getName()))->getMethods());
            return;
        }

        $allowedReturnTypes = [
            'array',
            'void',
            'null',
            'bool',
            'string',
            'int',
            'float',
            'double',
            'object',
            ArrayObject::class,
            StreamInterface::class,
            ResponseInterface::class,
            Response::class,
            MessageInterface::class,
            'mixed',
        ];

        if (!in_array($type->getName(), $allowedReturnTypes, true)) {
            throw new InterfaceMethodValidationException(
                InterfaceMethodValidationException::INVALID_METHOD_RETURN_TYPE,
                [implode('|', $allowedReturnTypes)],
            );
        }
    }
}
