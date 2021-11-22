<?php

namespace Waffler\Attributes\Request;

use Attribute;

/**
 * Class QueryParams
 *
 * An key-value array to be used as query strings.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Query
{
}
