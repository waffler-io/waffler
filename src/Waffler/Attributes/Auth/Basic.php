<?php

namespace Waffler\Attributes\Auth;

use Attribute;
use InvalidArgumentException;

/**
 * Class Basic.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Auth
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Basic
{
    public function check(mixed $value): void
    {
        (!is_array($value) || count($value) !== 2) &&
        throw new InvalidArgumentException(
            "The value of authorization must be an array with 2 values: username and password."
        );
    }
}
