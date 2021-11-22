<?php

namespace Waffler\Attributes\Request;

/**
 * Class JsonParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class JsonParam
{
    public function __construct(
        public string $key
    ) {
    }
}
