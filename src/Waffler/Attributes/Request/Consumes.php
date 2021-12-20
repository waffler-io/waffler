<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Attributes\Request;

use Attribute;

use JetBrains\PhpStorm\Pure;

use function Waffler\arrayWrap;

/**
 * Class Consumes.
 *
 * Add "Content-Type" headers.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Consumes extends Headers
{
    /**
     * @param string|array<string> $mimes
     */
    #[Pure]
    public function __construct(string|array $mimes)
    {
        parent::__construct([
            'Content-Type' => arrayWrap($mimes)
        ]);
    }
}
