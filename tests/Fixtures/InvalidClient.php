<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Fixtures;

use Waffler\Attributes\Verbs\Get;

/**
 * Class InvalidClient.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class InvalidClient
{
    #[Get('foo')]
    public function invalid(): void
    {
        //
    }
}
