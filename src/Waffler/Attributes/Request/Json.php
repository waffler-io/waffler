<?php

namespace Waffler\Attributes\Request;

use Attribute;

/**
 * Class Json.
 *
 * A key-value array to be used as JSON body.
 *
 * @see     \GuzzleHttp\RequestOptions::JSON
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Json
{
}
