<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Verbs;

use Attribute;

/**
 * Class Delete.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Delete extends AbstractHttpMethod
{
    public const NAME = 'DELETE';

    public function getName(): string
    {
        return self::NAME;
    }
}
