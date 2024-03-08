<?php

namespace Waffler\Waffler\Implementation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ImplHash
{
    public function __construct(
        public readonly string $hash,
    ) {}
}
