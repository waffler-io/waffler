<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Attributes\Auth;

use Attribute;
use Waffler\Attributes\Contracts\Auth;

/**
 * Class Ntml.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Auth
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Ntml implements Auth
{
    public function check(mixed $value): void
    {
        //
    }
}
