<?php

namespace Waffler\Attributes\Request;

use Attribute;

use JetBrains\PhpStorm\Pure;

use function Waffler\array_wrap;

/**
 * Class Headers.
 *
 * Add custom headers to the request.
 *
 * @see     \GuzzleHttp\RequestOptions::HEADERS
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Headers
{
    /**
     * @param array<string, string|array<string>> $headers
     */
    #[Pure]
    public function __construct(public array $headers = [])
    {
        foreach ($this->headers as $header => $value) {
            $this->headers[$header] = array_wrap($value);
        }
    }
}
