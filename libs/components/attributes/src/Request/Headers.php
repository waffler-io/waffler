<?php

/*
 * This file is part of Waffler\Component.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Request;

use Attribute;
use JetBrains\PhpStorm\Pure;

use function Waffler\Component\Helpers\arrayWrap;

/**
 * Class Headers.
 *
 * Add custom headers to the request.
 *
 * @see     \GuzzleHttp\RequestOptions::HEADERS
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes\Request
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
            $this->headers[$header] = arrayWrap($value);
        }
    }
}
