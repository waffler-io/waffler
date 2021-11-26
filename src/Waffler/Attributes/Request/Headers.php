<?php

namespace Waffler\Attributes\Request;

use Attribute;

use function Waffler\array_wrap;

/**
 * Class Headers.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Headers
{
    /**
     * @param array<string, string|array<string>> $headers
     */
    public function __construct(public array $headers = [])
    {
        foreach ($this->headers as $header => $value) {
            $this->headers[$header] = array_wrap($value);
        }
    }
}
