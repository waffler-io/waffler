<?php

namespace Waffler\Waffler\Implementation\Factory;

interface FactoryInterface
{
    /**
     * @param class-string<T> $interface
     *
     * @return string
     * @throws \ReflectionException
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T of object
     */
    public function generateForInterface(string $interface): string;
}
