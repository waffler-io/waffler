<?php

namespace Waffler\Attributes\Verbs;

use Attribute;

/**
 * Class Get.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Attributes\Verbs
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Get extends AbstractHttpMethod
{
    const NAME = 'GET';

    public function getName(): string
    {
        return self::NAME;
    }
}
