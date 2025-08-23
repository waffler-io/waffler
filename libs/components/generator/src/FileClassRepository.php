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

use ArrayAccess;
use Closure;
use Waffler\Component\Generator\Repositories\Exceptions\ClassNotFoundException;
use Waffler\Contracts\Generator\ClassRepositoryInterface;
use Waffler\Contracts\Generator\DataTransferObjects\CachedClassInterface;

class FileClassRepository implements ClassRepositoryInterface
{
    /**
     * @var array<string, bool>|ArrayAccess<string, bool>
     */
    protected array|ArrayAccess $classExistsCache = [];

    public function __construct(
        private readonly ClassNameGeneratorInterface $classNameGenerator = new MemoryCachedClassNameGenerator(
            new ClassNameGenerator(),
        ),
        private readonly string $cacheDirectory = GeneratorDefaults::IMPL_CACHE_DIRECTORY,
    ) {}

    public function has(string $interfaceFqn): bool
    {
        return $this->hasInCacheOr($interfaceFqn, file_exists(...));
    }

    /**
     * @param class-string<T>                   $interfaceFqn
     * @param (Closure(non-empty-string): bool) $default
     *
     * @return bool
     * @template T of object
     */
    protected function hasInCacheOr(string $interfaceFqn, Closure $default): bool
    {
        $filename = $this->buildFilename($interfaceFqn);
        return $this->classExistsCache[$filename] ??= $default($filename);
    }

    public function save(string $interfaceFqn, string $source): CachedClassInterface
    {
        $filename = $this->buildFilename($interfaceFqn);
        file_put_contents($filename, $source);
        $this->classExistsCache[$filename] = true;
        return new CachedClass(
            $interfaceFqn,
            $this->classNameGenerator->generateClassFqn($interfaceFqn),
        );
    }

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
     */
    protected function buildFilename(string $interfaceFqn): string
    {
        $className = $this->classNameGenerator->generateClassName($interfaceFqn);
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . $className . ".php";
    }
}
