<?php

namespace Waffler\Tests\Generator;

use Mockery\MockInterface;
use Waffler\Generator\AnonymousClassGenerator;
use PHPUnit\Framework\TestCase;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;
use Waffler\Tests\Generator\TestCaseTools\BasicMethodCallHandler;
use Waffler\Tests\Generator\TestCaseTools\InterfaceOne;

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

    /**
     * @throws \ReflectionException
     */
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