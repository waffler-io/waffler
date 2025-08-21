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

namespace Waffler\Component\Generator\Factory;

final readonly class FactoryDefaults
{
    public const string IMPL_CACHE_DIRECTORY = __DIR__ . '/../../generated';

    public const string NAMESPACE = "Waffler\\Component\\Generator\\Generated";
}
