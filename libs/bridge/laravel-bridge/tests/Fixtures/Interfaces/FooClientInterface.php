<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Bridge\Laravel\Tests\Fixtures\Interfaces;

use Waffler\Component\Attributes\Verbs\Get;

interface FooClientInterface
{
    #[Get('/foo')]
    public function getFoo(): array;
}
