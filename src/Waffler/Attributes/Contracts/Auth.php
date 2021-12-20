<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

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
