<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Fixtures\Interfaces;

use Waffler\Attributes\Request\PathParam;
use Waffler\Attributes\Verbs\Get;

/**
 * Interface NestedResourceClient.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface NestedResourceClient
{
    /**
     * @param int $barId
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    #[Get('bar/{barId}')]
    public function getBarById(#[PathParam] int $barId): string;
}
