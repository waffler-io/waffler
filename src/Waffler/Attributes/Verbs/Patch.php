<?php

namespace Waffler\Attributes\Verbs;

use Attribute;

/**
 * Class Patch.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Patch extends AbstractHttpMethod
{
    const NAME = 'PATCH';

    public function getName(): string
    {
        return self::NAME;
    }
}
