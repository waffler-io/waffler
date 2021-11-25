<?php

namespace Waffler\Tests\Generator\TestCaseTools;

use Waffler\Generator\Contracts\MethodCallHandler;

class BasicMethodCallHandler implements MethodCallHandler
{
    public function __call(string $name, array $arguments): mixed
    {
        return $arguments[0];
    }
}