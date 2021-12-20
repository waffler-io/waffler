<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Attributes\Utils;

use Attribute;

/**
 * Class Unwrap
 *
 * Tells the client to retrieve a specific key of the response.
 * It's very useful when the response data is inside of a root object.
 *
 * <pre>
 * // In this example, the data we need is wrapped in a root object.
 * {
 *    "status": "FOUND",
 *    "data": { ... }
 * }
 * </pre>
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Unwrap
{
    /**
     * Unwrap constructor.
     *
     * @param string $property The name of the internal object to extract.
     */
    public function __construct(
        public string $property = 'data'
    ) {
    }
}
