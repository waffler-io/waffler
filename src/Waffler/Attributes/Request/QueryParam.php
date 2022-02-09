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
use Waffler\Waffler\Attributes\Contracts\KeyedAttribute;

/**
 * Class QueryParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParam implements KeyedAttribute
{
    public function __construct(
        public string $key
    ) {
    }


    public function getKey(): string
    {
        return $this->key;
    }
}
