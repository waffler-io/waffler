<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\Group;
use ReflectionException;
use Waffler\Component\Client\Tests\Fixtures\CleanStart;
use Waffler\Component\Client\Tests\Fixtures\CrudTestCaseClient;
use Waffler\Component\Generator\GeneratorDefaults;

#[Group('waffler/client')]
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
        $glob = fn() => glob(GeneratorDefaults::IMPL_CACHE_DIRECTORY.'/*CrudTestCaseClient*.php');
        self::assertCount(0, $glob());
        $this->factory->warmup(CrudTestCaseClient::class);
        self::assertCount(1, $glob());
    }
}
