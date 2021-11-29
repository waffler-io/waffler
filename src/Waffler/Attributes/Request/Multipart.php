<?php

namespace Waffler\Attributes\Request;

use Attribute;

/**
 * Class Multipart.
 *
 * @see     \GuzzleHttp\RequestOptions::MULTIPART
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Multipart
{
}
