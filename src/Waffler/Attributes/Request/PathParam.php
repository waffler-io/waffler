<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Attributes\Request;

use Attribute;

/**
 * Class PathParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class PathParam
{
    /**
     * @param string|null $name If null, the name of the annotated parameter will be used.
     */
    public function __construct(
        public ?string $name = null
    ) {
    }
}
