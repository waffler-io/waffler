<?php

namespace Waffler\Attributes\Request;

use Attribute;

/**
 * Class QueryParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParam
{
    public function __construct(
        public string $key
    ) {
    }
}
