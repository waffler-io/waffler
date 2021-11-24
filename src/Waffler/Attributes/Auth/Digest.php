<?php

namespace Waffler\Attributes\Auth;

use Attribute;
use Waffler\Attributes\Contracts\Auth;

/**
 * Class Digest.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Auth
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Digest implements Auth
{
    public function check(mixed $value): void
    {
        //
    }
}
