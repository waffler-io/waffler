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
use Waffler\Contracts\Generator\DataTransferObjects\CachedClassInterface;

/**
 * Class CachedClass.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template-implements CachedClassInterface<I, C>
 * @template I
 * @template C of I
 */
final readonly class CachedClass implements CachedClassInterface
{
    /**
     * @param class-string<I> $interfaceFqn
     * @param class-string<C> $classFqn
     */
    public function __construct(
        public string $interfaceFqn,
        public string $classFqn,
    ) {}
}
