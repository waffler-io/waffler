<?php

namespace Waffler\Tests\Unit\Client;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Waffler\Client\Factory;
use Waffler\Tests\Tools\CrudTestCaseClient;
use Waffler\Tests\Tools\InvalidType;

/**
 * Class FactoryTest.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @covers \Waffler\Client\Factory
 */
class FactoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testMustRejectNonInterfaceClassString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(10);
        Factory::make(InvalidType::class);
    }

    public function testMustRejectNonObjectLikeTypeName(): void
    {
        $this->expectException(\ReflectionException::class);
        Factory::make('string'); //@phpstan-ignore-line
    }
}