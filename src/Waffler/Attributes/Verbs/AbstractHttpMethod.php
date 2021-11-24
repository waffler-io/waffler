<?php

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
        public string $path
    )
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
