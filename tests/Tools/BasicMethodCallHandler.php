<?php

namespace Waffler\Tests\Tools;

use Waffler\Generator\Contracts\MethodCallHandler;

class BasicMethodCallHandler implements MethodCallHandler
{
    public function __call(string $name, array $arguments): mixed
    {
        return $arguments[0];
    }
}