<?php

namespace Waffler\Attributes\Request;

use Attribute;

use function Waffler\array_wrap;

/**
 * Class Produces.
 *
 * Add "Content-Type" headers.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Produces extends Headers
{
    /**
     * @param string|array<string> $mimes
     */
    public function __construct(string|array $mimes)
    {
        parent::__construct([
            'Accept' => array_wrap($mimes)
        ]);
    }
}