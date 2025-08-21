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
 * Interface Verb.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface Verb extends Pathable
{
    /**
     * Retrieves the verb name.
     *
     * @return string 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE' | 'HEAD'
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function getName(): string;
}
