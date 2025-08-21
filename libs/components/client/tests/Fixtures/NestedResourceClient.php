<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Fixtures;

use Waffler\Component\Attributes\Request\PathParam;
use Waffler\Component\Attributes\Verbs\Get;

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
