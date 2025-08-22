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

use ReflectionException;
use Waffler\Component\Generator\Repositories\Exceptions\ClassNotFoundException;
use Waffler\Contracts\Generator\ClassRepositoryInterface;
use Waffler\Contracts\Generator\DataTransferObjects\CachedClassInterface;

final class FileClassRepository implements ClassRepositoryInterface
{
    private ClassNameGenerator $classNameGenerator;

    /**
     * @var array<string, bool>
     */
    private array $classExistsCache = [];

    public function __construct(
        private readonly string $cacheDirectory = GeneratorDefaults::IMPL_CACHE_DIRECTORY,
        private readonly string $baseNamespace = GeneratorDefaults::NAMESPACE,
    ) {
        $this->classNameGenerator = new ClassNameGenerator($this->baseNamespace);
    }

    /**
     * @throws ReflectionException
     */
    public function has(string $interfaceFqn): bool
    {
        $filename = $this->buildFilename($interfaceFqn);
        return $this->classExistsCache[$filename] ??= file_exists($filename);
    }

    /**
     * @throws ReflectionException
     */
    public function save(string $interfaceFqn, string $source): CachedClassInterface
    {
        $filename = $this->buildFilename($interfaceFqn);
        file_put_contents($filename, $source);
        return new CachedClass(
            $interfaceFqn,
            $this->classNameGenerator->generateClassFqn($interfaceFqn),
        );
    }

    /**
     * @throws ReflectionException
     */
    public function get(string $interfaceFqn): CachedClassInterface
    {
        if (!$this->has($interfaceFqn)) {
            throw ClassNotFoundException::classDoesNotExists($interfaceFqn);
        }
        return new CachedClass(
            $interfaceFqn,
            $this->classNameGenerator->generateClassFqn($interfaceFqn),
        );
    }

    /**
     * @param class-string<T> $interfaceFqn
     *
     * @return non-empty-string
     * @template T of object
     * @throws ReflectionException
     */
    private function buildFilename(string $interfaceFqn): string
    {
        $className = $this->classNameGenerator->generateClassName($interfaceFqn);
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . $className . ".php";
    }
}
