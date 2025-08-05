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
use ReflectionException;
use Waffler\Waffler\Client\Factory;
use Waffler\Waffler\Tests\Fixtures\CleanStart;
use Waffler\Waffler\Tests\Fixtures\CrudTestCaseClient;

/**
 * Class FactoryTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Waffler\Client\Factory
 * @uses \Waffler\Waffler\Client\MethodInvoker
 * @uses \Waffler\Waffler\Client\Proxy
 */
class FactoryTest extends TestCase
{
    use CleanStart;
    use MockeryPHPUnitIntegration;

    public function testMustInstantiateValidImplementationForValidInterfaces(): void
    {
        $client = $this->factory->make(CrudTestCaseClient::class, []);
        self::assertInstanceOf(CrudTestCaseClient::class, $client);
    }

    /**
     * @throws ReflectionException
     */
    public function testMustGenerateAndSaveIntoDiskCache()
    {
        $glob = fn () => glob(self::IMPL_DIR.'/*CrudTestCaseClient*.php');
        self::assertCount(0, $glob());
        $this->factory->warmup(CrudTestCaseClient::class);
        self::assertCount(1, $glob());
    }
}
