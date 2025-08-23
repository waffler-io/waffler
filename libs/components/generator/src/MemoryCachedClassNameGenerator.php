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

use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;

final class MemoryCachedClassNameGenerator implements ClassNameGeneratorInterface
{
    /**
     * @var array<class-string, class-string>
     */
    private array $generatedClassFqn = [];

    /**
     * @var array<class-string, non-empty-string>
     */
    private array $generatedClassName = [];

    public function __construct(
        private readonly ClassNameGeneratorInterface $generator,
    ) {}

    public function generateClassFqn(string $interfaceFqn): string
    {
        if (array_key_exists($interfaceFqn, $this->generatedClassFqn)) {
            $value = $this->generatedClassFqn[$interfaceFqn];
            if ($value
                && is_subclass_of($value, $interfaceFqn)
                && is_subclass_of($value, WafflerImplConstructorInterface::class)) {
                return $value;
            }
        }
        return $this->generatedClassFqn[$interfaceFqn]
            = $this->generator->generateClassFqn($interfaceFqn);
    }

    public function generateClassName(string $interfaceFqn): string
    {
        return $this->generatedClassName[$interfaceFqn]
            ??= $this->generator->generateClassName($interfaceFqn);
    }
}
