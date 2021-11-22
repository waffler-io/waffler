<?php

namespace Waffler\Waffler\Contracts\Attributes;

/**
 * Interface Verb.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface Verb
{
    /**
     * Retrieves the verb name.
     *
     * @return string 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE' | 'HEAD'
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getName(): string;

    /**
     * Retrieves the URI path.
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getPath(): string;
}