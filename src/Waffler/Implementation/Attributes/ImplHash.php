<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ImplHash
{
    public function __construct(
        public readonly string $hash,
    ) {
    }
}
