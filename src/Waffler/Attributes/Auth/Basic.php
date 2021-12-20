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
use InvalidArgumentException;
use Waffler\Attributes\Contracts\Auth;

/**
 * Class Basic.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Auth
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Basic implements Auth
{
    public function check(mixed $value): void
    {
        if (!is_array($value) || count($value) !== 2) {
            throw new InvalidArgumentException(
                "The value of authorization must be an array with 2 values: username and password."
            );
        }
    }
}
