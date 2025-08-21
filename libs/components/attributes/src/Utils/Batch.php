<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Utils;

use Attribute;

/**
 * Class Batch.
 *
 * This attribute marks a function that makes multiple simultaneous requests.
 * Every batch method must accept an array as its unique argument. The array contents is a list of the arguments to be passed to the original method.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Batch
{
    /**
     * @param string $methodName The method name to be batched.
     */
    public function __construct(
        public string $methodName,
    ) {}
}
