<?php

namespace Waffler\Attributes\Verbs;

use Attribute;

/**
 * Class Put.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Put extends AbstractHttpMethod
{
    const NAME = 'PUT';

    public function getName(): string
    {
        return self::NAME;
    }
}
