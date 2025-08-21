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

namespace Waffler\Component\Generator\Traits;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Trait InteractsWithAttributes
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package  Waffler\Component\Generator\Traits
 * @internal
 */
trait InteractsWithAttributes
{
    /**
     * @param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @param class-string<TAttributeType>                                         $name
     * @param bool                                                                 $instanceOf
     *
     * @return bool
     * @template TParentType of object
     * @template TAttributeType of object
     */
    private function reflectionHasAttribute(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name,
        bool $instanceOf = false,
    ): bool {
        return count($reflection->getAttributes($name, $instanceOf ? ReflectionAttribute::IS_INSTANCEOF : 0)) !== 0;
    }

    /**
     * @param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @param class-string<TAttributeType>                                         $name
     * @param bool                                                                 $instanceOf
     *
     * @return object&TAttributeType
     * @template TAttributeType of object
     * @template TParentType of object
     */
    private function getAttributeInstance(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name,
        bool $instanceOf = false,
    ): object {
        return $this->getAttributeInstances($reflection, $name, $instanceOf)[0];
    }

    /**
     * @param \ReflectionMethod|\ReflectionParameter|\ReflectionClass<TParentType> $reflection
     * @param class-string<TAttributeType>                                         $name
     * @param bool                                                                 $instanceOf
     *
     * @return array<int, object&TAttributeType>
     * @template TAttributeType of object
     * @template TParentType of object
     */
    private function getAttributeInstances(
        ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        string $name,
        bool $instanceOf = false,
    ): array {
        return array_map(
            fn(ReflectionAttribute $attribute) => $attribute->newInstance(),
            $reflection->getAttributes($name, $instanceOf ? ReflectionAttribute::IS_INSTANCEOF : 0),
        );
    }
}
