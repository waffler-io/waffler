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

namespace Waffler\Component\Generator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;

/**
 * Class ClassNameGenerator.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @internal
 */
final class ClassNameGenerator implements ClassNameGeneratorInterface
{
    public function __construct(
        private string $baseNamespace = GeneratorDefaults::NAMESPACE,
    ) {}

    /**
     * @param class-string<T> $interfaceFqn
     *
     * @return class-string<covariant T&WafflerImplConstructorInterface>
     * @template T of object
     */
    public function generateClassFqn(string $interfaceFqn): string
    {
        $className = $this->generateClassName($interfaceFqn);
        /**
         * @var class-string<covariant T&WafflerImplConstructorInterface> $fqn
         */
        $fqn = $this->baseNamespace . '\\' . $className;
        return $fqn;
    }

    /**
     * @param class-string<covariant T> $interfaceFqn
     *
     * @return non-empty-string
     * @template T of object
     */
    public function generateClassName(string $interfaceFqn): string
    {
        $reflectionInterface = new ReflectionClass($interfaceFqn);
        return str_replace('\\', '_', $interfaceFqn)
            . $this->generateInterfaceHash($reflectionInterface)
            . 'Impl';
    }

    /**
     * @param ReflectionClass<T> $interface
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T of object
     */
    private function generateInterfaceHash(ReflectionClass $interface): string
    {
        $methods = [];
        foreach ($interface->getMethods() as $method) {
            $methodSignature = [
                'name' => $method->getName(),
                'return' => $this->serializeReflectionType($method->getReturnType()),
                'attributes' => array_map(
                    static fn(ReflectionAttribute $attribute) => [
                        'name' => $attribute->getName(),
                        'arguments' => $attribute->getArguments(),
                    ],
                    $method->getAttributes(),
                ),
                'parameters' => array_map(
                    function (ReflectionParameter $param) {
                        return [
                            'name' => $param->getName(),
                            'position' => $param->getPosition(),
                            'type' => $this->serializeReflectionType($param->getType()),
                            'isVariadic' => $param->isVariadic(),
                            'isOptional' => $param->isOptional(),
                            'allowsNull' => $param->allowsNull(),
                            'defaultValue' => $param->isDefaultValueAvailable(),
                            'attributes' => array_map(
                                static fn(ReflectionAttribute $attribute) => [
                                    'name' => $attribute->getName(),
                                    'arguments' => $attribute->getArguments(),
                                ],
                                $param->getAttributes(),
                            ),
                        ];
                    },
                    $method->getParameters(),
                ),
            ];
            $methods[] = $methodSignature;
        }

        return md5(serialize($methods));
    }

    private function serializeReflectionType(?ReflectionType $reflectionType): ?string
    {
        if ($reflectionType instanceof ReflectionNamedType) {
            return $reflectionType->getName();
        }
        if ($reflectionType instanceof ReflectionUnionType) {
            return implode(
                '|',
                array_map($this->serializeReflectionType(...), $reflectionType->getTypes()),
            );
        }
        if ($reflectionType instanceof ReflectionIntersectionType) {
            return implode(
                '&',
                array_map($this->serializeReflectionType(...), $reflectionType->getTypes()),
            );
        }
        return null;
    }
}
