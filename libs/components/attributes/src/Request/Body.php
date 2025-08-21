<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Request;

use Attribute;
use JetBrains\PhpStorm\Pure;

use function Waffler\Component\Helpers\arrayWrap;

/**
 * Class Body.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Body
{
    /**
     * @param non-empty-string|array<non-empty-string> $mimeTypes
     */
    #[Pure]
    public function __construct(
        private string|array $mimeTypes = [],
    ) {}

    /**
     * @return array<string>
     */
    #[Pure]
    public function getMimeTypes(): array
    {
        return arrayWrap($this->mimeTypes);
    }
}
