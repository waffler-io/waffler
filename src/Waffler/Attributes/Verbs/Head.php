<?php

namespace Waffler\Attributes\Verbs;

use Attribute;

/**
 * Class Head.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Head extends AbstractHttpMethod
{
    const NAME = 'HEAD';

    public function getName(): string
    {
        return self::NAME;
    }
}
