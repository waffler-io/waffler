<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Contracts\Attributes;

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
