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
 * Interface ArraySettable.
 *
 * If an attribute implements this interface, we can use the key as an array set value.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface ArraySettable
{
    /**
     * Retrieves the path separator to be used in arraySet function.
     *
     * @return string
     * @see \Waffler\Waffler\arraySet()
     *@author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getPathSeparator(): string;
}
