<?php

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
    public function __construct(
        public string $name
    ) {
    }
}
