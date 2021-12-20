<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Attributes\Request;

use Waffler\Attributes\Contracts\Pathable;

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
class Path implements Pathable
{
    public function __construct(
        public string $path
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
