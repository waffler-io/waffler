<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Tests\Unit\Client\Pipeline\Stages;

use Mockery;
use ReflectionClass;
use Waffler\Client\Pipeline\Stages\CreateMethodCallProxy;
use PHPUnit\Framework\TestCase;
use Waffler\Client\Proxy;

/**
 * Class CreateMethodCallProxyTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Pipeline\Stages\CreateMethodCallProxy
 * @uses \Waffler\Client\MethodInvoker
 * @uses \Waffler\Client\Proxy
 */
class CreateMethodCallProxyTest extends TestCase
{
    public function testItMustReturnAnInstanceOfCreateMethodCallProxy(): void
    {
        $this->assertInstanceOf(
            Proxy::class,
            (new CreateMethodCallProxy([]))
                ->handle(Mockery::mock(ReflectionClass::class))
        );
    }
}
