<?php

namespace Waffler\Attributes\Request;

use Attribute;

/**
 * Class PathParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
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
