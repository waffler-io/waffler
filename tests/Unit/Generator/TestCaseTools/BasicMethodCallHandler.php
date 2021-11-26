<?php

namespace Waffler\Tests\Unit\Generator\TestCaseTools;

use Waffler\Generator\Contracts\MethodCallHandler;

class BasicMethodCallHandler implements MethodCallHandler
{
    public function __call(string $name, array $arguments): mixed
    {
        return $arguments[0];
    }
}