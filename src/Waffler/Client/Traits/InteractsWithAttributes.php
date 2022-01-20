<?php

declare(strict_types=1);

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client\Traits;

use JetBrains\PhpStorm\Pure;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Trait InteractsWithAttributes
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package  Waffler\Client\Traits
 * @internal
 */
trait InteractsWithAttributes
{
    /**
     * @psalm-param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @psalm-param class-string                                                         $name
     *
     * @return bool
     * @psalm-template TParentType of object
     */
    private function reflectionHasAttribute(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name
    ): bool {
        return !empty($reflection->getAttributes($name));
    }

    /**
     * @psalm-param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @psalm-param class-string<TAttributeType>                                         $name
     *
     * @return TAttributeType
     * @psalm-template TAttributeType of object
     * @psalm-template TParentType of object
     */
    private function getAttributeInstance(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name
    ): object {
        return $this->getAttributeInstances($reflection, $name)[0];
    }

    /**
     * @psalm-param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @psalm-param class-string<TAttributeType>                                         $name
     *
     * @return array<int, TAttributeType>
     * @psalm-template TAttributeType of object
     * @psalm-template TParentType of object
     */
    private function getAttributeInstances(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name
    ): array {
        return array_map(
            fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
            $reflection->getAttributes($name)
        );
    }
}
