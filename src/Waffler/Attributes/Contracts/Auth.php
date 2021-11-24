<?php

namespace Waffler\Attributes\Contracts;

/**
 * Interface Auth.
 *
 * Represents all attributes that performs authentication.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface Auth
{
    /**
     * @param mixed $value
     *
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @throws \InvalidArgumentException If the given value is rejected by the attribute.
     */
    public function check(mixed $value): void;
}