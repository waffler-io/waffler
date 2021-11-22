<?php

namespace Waffler\Attributes\Verbs;

use Waffler\Contracts\Attributes\Verb;

/**
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
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
