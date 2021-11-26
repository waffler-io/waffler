<?php

namespace Waffler\Tests\Unit\Generator;

use Mockery\MockInterface;
use Waffler\Generator\AnonymousClassGenerator;
use PHPUnit\Framework\TestCase;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;
use Waffler\Tests\Unit\Generator\TestCaseTools\BasicMethodCallHandler;
use Waffler\Tests\Unit\Generator\TestCaseTools\InterfaceOne;

/**
 * @covers \Waffler\Generator\AnonymousClassGenerator
 */
class AnonymousClassGeneratorTest extends TestCase
{
    /**
     * @var \Waffler\Generator\Contracts\InterfaceInstantiator<InterfaceOne>
     */
    private InterfaceInstantiator $instantiator;

    private MethodCallHandler $methodCallHandler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var AnonymousClassGenerator<InterfaceOne> */
        $anonymousClassGenerator = new AnonymousClassGenerator();
        $this->instantiator = $anonymousClassGenerator;
        $this->methodCallHandler = new BasicMethodCallHandler();
    }

    public function testItMustInstantiateAndReturnTheSameValueAsFirstArgument(): void
    {
        $obj = $this->instantiator->instantiate(
            InterfaceOne::class,
            $this->methodCallHandler
        );

        $value = 'foo';

        $response = $obj->test($value);

        self::assertEquals($response, $value);
    }
}