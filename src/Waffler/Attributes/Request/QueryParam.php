<?php

namespace Waffler\Waffler\Attributes\Request;

use Attribute;

/**
 * Class QueryParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParam
{
    public function __construct(
        public string $key
    ) {
    }
}
