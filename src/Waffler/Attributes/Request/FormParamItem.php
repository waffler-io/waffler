<?php

namespace Waffler\Waffler\Attributes\Request;

use Attribute;

/**
 * Class FormParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class FormParamItem
{
    public function __construct(
        public string $key
    ) {
    }
}
