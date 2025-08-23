<?php

declare(strict_types=1);

namespace Waffler\Bridge\Laravel\Tests\Fixtures\Interfaces;

use Waffler\Component\Attributes\Verbs\Get;

interface SimpleInterface
{
    #[Get('/')]
    public function foo(): bool;
}
