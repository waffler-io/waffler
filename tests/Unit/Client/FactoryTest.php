<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Unit\Client;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Waffler\Waffler\Client\Factory;
use Waffler\Waffler\Implementation\Exceptions\NotAnInterfaceException;
use Waffler\Waffler\Implementation\Factory\FactoryInterface;
use Waffler\Waffler\Tests\Fixtures\CrudTestCaseClient;
use Waffler\Waffler\Tests\Fixtures\Interfaces\InterfaceWithValidMethodSignature;
use Waffler\Waffler\Tests\Fixtures\InvalidType;

/**
 * Class FactoryTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\Factory
 * @uses \Waffler\Waffler\Client\MethodInvoker
 * @uses \Waffler\Waffler\Generator\AnonymousClassGenerator
 * @uses \Waffler\Waffler\Generator\AnonymousClassMethod
 * @uses \Waffler\Waffler\Generator\FactoryFunction
 * @uses \Waffler\Waffler\Client\Proxy
 */
class FactoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testMustGenerateValidImplementationForValidInterfaces(): void
    {
        $client = (new Factory())->make(CrudTestCaseClient::class, []);

        self::assertInstanceOf(CrudTestCaseClient::class, $client);
    }
}
