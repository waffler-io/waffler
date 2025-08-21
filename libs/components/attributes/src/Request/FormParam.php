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

use Attribute;
use Waffler\Contracts\Attributes\KeyedAttribute;

/**
 * Class FormParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class FormParam implements KeyedAttribute
{
    public function __construct(
        public string $key,
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }
}
