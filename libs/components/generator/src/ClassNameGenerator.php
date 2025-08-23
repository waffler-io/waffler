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

use ReflectionClass;
use ReflectionException;
use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;
use Waffler\Component\Generator\Exceptions\NotAnInterfaceException;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

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
            . md5_file($reflectionInterface->getFileName()) //@phpstan-ignore-line
            . 'Impl';
    }
}
