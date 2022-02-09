<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Attributes\Contracts;

/**
 * Interface KeyedAttribute.
 *
 * Indicates that the attribute holds a key to be used in an array.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface KeyedAttribute
{
    /**
     * Retrieves the name of the key.
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getKey(): string;
}
