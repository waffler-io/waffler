<?php

declare(strict_types = 1);

namespace Waffler\Client\Traits;

use JetBrains\PhpStorm\Pure;
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
     * @param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @param class-string                                                         $name
     *
     * @return bool
     * @phpstan-template TParentType of object
     */
    #[Pure]
    private function reflectionHasAttribute(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name
    ): bool {
        return !empty($reflection->getAttributes($name));
    }

    /**
     * @param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @param class-string<TAttributeType>                                         $name
     *
     * @return TAttributeType
     * @phpstan-template TAttributeType of object
     * @phpstan-template TParentType of object
     */
    private function getAttributeInstance(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name
    ): object {
        return $this->getAttributeInstances($reflection, $name)[0];
    }

    /**
     * @param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @param class-string<TAttributeType>                                         $name
     *
     * @return array<TAttributeType>
     * @phpstan-template TAttributeType of object
     * @phpstan-template TParentType of object
     */
    private function getAttributeInstances(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name
    ): array {
        $instances = [];
        foreach ($reflection->getAttributes($name) as $attribute) {
            $instances[] = $attribute->newInstance();
        }
        return $instances;
    }
}
