<?php

namespace Waffler\Waffler\Attributes\Request;

use Attribute;

/**
 * Class HeaderParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class HeaderParam
{
    public function __construct(
        public string $headerName
    ) {
    }
}
