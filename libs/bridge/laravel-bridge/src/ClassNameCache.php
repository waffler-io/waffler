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

namespace Waffler\Bridge\Laravel;

use DateInterval;
use Illuminate\Contracts\Config\Repository;
use Psr\SimpleCache\CacheInterface;

/**
 * Class ClassNameCache.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @internal
 */
final readonly class ClassNameCache implements CacheInterface
{
    public function __construct(
        private string $prefix,
        private Repository $repository,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->repository->get("{$this->prefix}.$key", $default);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->repository->set("{$this->prefix}.$key", $value);
        return true;
    }

    public function delete(string $key): bool
    {
        $this->repository->set("{$this->prefix}.$key", null);
        return true;
    }

    public function clear(): bool
    {
        $this->repository->set("{$this->prefix}", [
            'fqn' => [],
            'class_name' => [],
        ]);
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
        return [];
    }

    /**
     * @param iterable<string, mixed> $values
     * @param DateInterval|int|null   $ttl
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has(string $key): bool
    {
        return $this->repository->has("{$this->prefix}.$key");
    }
}
