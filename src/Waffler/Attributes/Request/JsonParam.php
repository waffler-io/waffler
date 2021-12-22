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
use Waffler\Attributes\Contracts\ArraySettable;
use Waffler\Attributes\Contracts\KeyedAttribute;

/**
 * Class JsonParam.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class JsonParam implements KeyedAttribute, ArraySettable
{
    public function __construct(
        public string $key
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPathSeparator(): string
    {
        return '.';
    }
}
