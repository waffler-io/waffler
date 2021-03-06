<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Fixtures\Interfaces;

use Waffler\Waffler\Attributes\Request\PathParam;
use Waffler\Waffler\Attributes\Verbs\Get;

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
