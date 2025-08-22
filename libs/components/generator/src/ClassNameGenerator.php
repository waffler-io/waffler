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

/**
 * Class ClassNameGenerator.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @internal
 */
final class ClassNameGenerator
{
    /**
     * @var array<class-string, non-empty-string>
     */
    private array $cache = [];

    public function __construct(
        private string $baseNamespace = GeneratorDefaults::NAMESPACE,
    ) {}

    /**
     * @param class-string<T> $interfaceFqn
     *
     * @return class-string<covariant T&WafflerImplConstructorInterface>
     * @throws ReflectionException
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
     * @throws ReflectionException
     * @template T of object
     */
    public function generateClassName(string $interfaceFqn): string
    {
        if (array_key_exists($interfaceFqn, $this->cache)) {
            return $this->cache[$interfaceFqn];
        }
        $reflectionInterface = new ReflectionClass($interfaceFqn);
        $className = str_replace('\\', '_', $interfaceFqn)
            . md5_file($reflectionInterface->getFileName()) //@phpstan-ignore-line
            . 'Impl';
        $this->cache[$interfaceFqn] = $className;
        return $className;
    }
}
