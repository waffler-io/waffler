<?php

namespace Waffler\Attributes\Request;

use function Waffler\array_wrap;

/**
 * Class Consumes.
 *
 * Add "Accept" headers.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Consumes extends Headers
{
    /**
     * @param string|array<string> $mimes
     */
    public function __construct(string|array $mimes)
    {
        parent::__construct([
            'Content-Type' => array_wrap($mimes)
        ]);
    }
}