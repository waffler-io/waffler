<?php

namespace Waffler\Attributes\Auth;

use Attribute;
use Waffler\Attributes\Contracts\Auth;

/**
 * Class Bearer.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Auth
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Bearer implements Auth
{
    public function check(mixed $value): void
    {
        //
    }
}
