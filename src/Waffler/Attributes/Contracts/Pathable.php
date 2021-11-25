<?php

namespace Waffler\Attributes\Contracts;

/**
 * Interface Pathable.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface Pathable
{
    /**
     * Retrieves the URI path.
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getPath(): string;
}