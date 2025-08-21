<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Request;

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
        public int $timeout = 0,
    ) {}
}
