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

use Waffler\Component\Generator\ClassNameGeneratorInterface;
use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;

final readonly class ConfigCachedClassNameGenerator implements ClassNameGeneratorInterface
{
    public function __construct(
        private ClassNameGeneratorInterface $generator,
    ) {}

    public function generateClassFqn(string $interfaceFqn): string
    {
        $config = "waffler-cache.fqn.$interfaceFqn";
        /**
         * @var class-string|null $cached
         */
        $cached = config()->get($config);
        if ($cached
            && is_subclass_of($cached, $interfaceFqn)
            && is_subclass_of($cached, WafflerImplConstructorInterface::class)) {
            return $cached;
        }
        $result = $this->generator->generateClassFqn($interfaceFqn);
        config()->set($config, $result);
        return $result;
    }

    public function generateClassName(string $interfaceFqn): string
    {
        $config = "waffler-cache.class_name.$interfaceFqn";
        /**
         * @var non-empty-string|null $cached
         */
        $cached = config()->get($config);
        if ($cached) {
            return $cached;
        }
        $result = $this->generator->generateClassName($interfaceFqn);
        config()->set($config, $result);
        return $result;
    }
}
