<?php

namespace Waffler\Waffler\Attributes\Verbs;

use Waffler\Waffler\Contracts\Attributes\Verb;

/**
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Verbs
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
