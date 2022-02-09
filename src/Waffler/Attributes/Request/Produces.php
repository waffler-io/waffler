<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Attributes\Request;

use Attribute;
use JetBrains\PhpStorm\Pure;

use function Waffler\Waffler\arrayWrap;

/**
 * Class Produces.
 *
 * Add "Accept" headers.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Produces extends Headers
{
    /**
     * @param string|array<string> $mimes
     */
    #[Pure]
    public function __construct(string|array $mimes)
    {
        parent::__construct([
            'Accept' => arrayWrap($mimes)
        ]);
    }
}
