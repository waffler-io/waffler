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

use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class MemoryCache.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-type CacheValue array{value: T, ttl: DateInterval|null}
 * @template T of mixed
 */
final class MemoryCache implements CacheInterface
{
    /**
     * @phpstan-var array<string, CacheValue>
     */
    private array $cache = [];

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return T
     * @throws InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            $value = $this->cache[$key];
            $hasValidTtl = !$value['ttl'] instanceof DateInterval
                || new DateTime() < new DateTime()->add($value['ttl']);
            if ($hasValidTtl) {
                return $value['value'];
            }
        }
        $this->delete($key);
        return $default;
    }

    /**
     * @param string                $key
     * @param T                     $value
     * @param DateInterval|int|null $ttl
     *
     * @return bool
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        if (is_int($ttl)) {
            $ttl = new DateInterval("PT{$ttl}S");
        }
        $this->cache[$key] = [
            'value' => $value,
            'ttl' => $ttl,
        ];
        return true;
    }

    public function delete(string $key): bool
    {
        if ($this->has($key)) {
            unset($this->cache[$key]);
            return true;
        }
        return false;
    }

    public function clear(): bool
    {
        $this->cache = [];
        return true;
    }

    /**
     * @param iterable<string> $keys
     * @param mixed|null       $default
     *
     * @return iterable<string, T>
     * @throws InvalidArgumentException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
        return [];
    }

    /**
     * @param iterable<string, T> $values
     * @param DateInterval|int|null   $ttl
     *
     * @return bool
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result = $result && $this->set($key, $value, $ttl);
        }
        return $result;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->delete($key);
        }
        return $result;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }
}
