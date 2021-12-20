<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Attributes\Verbs;

use Waffler\Attributes\Contracts\Verb;

/**
 * Base http method attribute class.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
 * @internal For internal use only.
 */
abstract class AbstractHttpMethod implements Verb
{
    public function __construct(
        public string $path = '/'
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
