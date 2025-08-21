<?php

declare(strict_types=1);

namespace Waffler\Component\Helpers\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Waffler\Component\Helpers\Arr;

#[Group('waffler/helpers')]
class ArrTest extends TestCase
{
    #[Test]
    public function shouldRetrieveAnElementFromArbitraryDepth(): void
    {
        $arr = ['foo' => ['bar' => ['baz' => 'qux']]];
        self::assertEquals('qux', Arr::get($arr, 'foo.bar.baz'));
    }

    #[Test]
    public function shouldRetrieveAnElementFromArbitraryDepthWithWithArbitraryPathSeparator(): void
    {
        $arr = ['foo' => ['bar' => ['baz' => 'qux']]];
        self::assertEquals('qux', Arr::get($arr, 'foo->bar->baz', '->'));
    }

    #[Test]
    public function shouldPutAnElementInsideAnArbitraryPathInsideTheArray(): void
    {
        $arr = ['foo' => ['bar' => ['baz' => 'qux']]];
        Arr::set($arr, 'foo.bar.baz', 'quux');
        self::assertEquals('quux', $arr['foo']['bar']['baz']);
    }

    #[Test]
    public function shouldPutAnElementInsideAnArbitraryPathInsideTheArrayWithWithArbitraryPathSeparator(): void
    {
        $arr = ['foo' => ['bar' => ['baz' => 'qux']]];
        Arr::set($arr, 'foo->bar->baz', 'quux', '->');
        self::assertEquals('quux', $arr['foo']['bar']['baz']);
    }

    #[Test]
    public function shouldWrapTheNonArrayValue()
    {
        self::assertEquals([1], Arr::wrap(1));
    }

    #[Test]
    public function shouldNotWrapTheArrayValue()
    {
        self::assertEquals([1, 2, 3], Arr::wrap([1, 2, 3]));
    }
}
