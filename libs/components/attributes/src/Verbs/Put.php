<?php

/*
 * This file is part of Waffler\Component.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Verbs;

use Attribute;

/**
 * Class Put.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Put extends AbstractHttpMethod
{
    public const NAME = 'PUT';

    public function getName(): string
    {
        return self::NAME;
    }
}
