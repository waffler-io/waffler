<?php

namespace Waffler\Attributes\Verbs;

use Attribute;

/**
 * Class Post.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Post extends AbstractHttpMethod
{
    const NAME = 'POST';

    public function getName(): string
    {
        return self::NAME;
    }
}
