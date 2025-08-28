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

use Closure;
use Psr\SimpleCache\CacheInterface;

final readonly class CachedClassNameGenerator implements ClassNameGeneratorInterface
{
    public function __construct(
        private CacheInterface $cache,
        private ClassNameGeneratorInterface $generator,
    ) {}

    public function generateClassFqn(string $interfaceFqn): string
    {
        return $this->getOrCache(
            "fqn.$interfaceFqn",
            fn() => $this->generator->generateClassFqn($interfaceFqn),
        );
    }

    public function generateClassName(string $interfaceFqn): string
    {
        return $this->getOrCache(
            "class_name.$interfaceFqn",
            fn() => $this->generator->generateClassName($interfaceFqn),
        );
    }

    /**
     * @param non-empty-string  $key
     * @param (Closure(): T) $default
     *
     * @return T
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T of non-empty-string
     */
    private function getOrCache(string $key, Closure $default): string
    {
        if ($this->cache->has($key)) {
            return $this->cache->get($key); // @phpstan-ignore-line
        }
        $value = $default();
        $this->cache->set($key, $value);
        return $value;
    }
}
