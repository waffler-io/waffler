<?php

namespace Waffler\Attributes\Request;

/**
 * Class Path.
 *
 * If the attribute is located on the class, the path is concatenated
 * after base_uri and applied to all methods.
 *
 * If the attribute is located on the method, the path is just added after the base_uri
 * just for this specific method.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Path
{
    public function __construct(
        public string $path
    ) { }
}