<?php

namespace Waffler\Attributes\Request;

/**
 * Class Timeout.
 *
 * @see    \GuzzleHttp\RequestOptions::TIMEOUT
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Timeout
{
    public function __construct(
        public int $timeout = 0
    )
    {
    }
}