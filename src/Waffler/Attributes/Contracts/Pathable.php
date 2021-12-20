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
