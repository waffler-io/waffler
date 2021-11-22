<?php

namespace Waffler\Waffler\Attributes\Verbs;

use Attribute;

/**
 * Class Delete.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Waffler\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Delete extends AbstractHttpMethod
{
    const NAME = 'DELETE';

    public function getName(): string
    {
        return self::NAME;
    }
}
