<?php

namespace Waffler\Tests\Unit\Generator;

use Mockery\MockInterface;
use Waffler\Generator\AnonymousClassGenerator;
use PHPUnit\Framework\TestCase;
use Waffler\Generator\Contracts\InterfaceInstantiator;
use Waffler\Generator\Contracts\MethodCallHandler;
use Waffler\Tests\Tools\BasicMethodCallHandler;
use Waffler\Tests\Tools\Interfaces\InterfaceWithValidMethodSignature;

/**
 * @covers \Waffler\Generator\AnonymousClassGenerator
 */
class AnonymousClassGeneratorTest extends TestCase
{
    /**
     * @var \Waffler\Generator\Contracts\InterfaceInstantiator<InterfaceWithValidMethodSignature>
     */
    private InterfaceInstantiator $instantiator;

    private MethodCallHandler $methodCallHandler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var AnonymousClassGenerator<InterfaceWithValidMethodSignature> */
        $anonymousClassGenerator = new AnonymousClassGenerator();
        $this->instantiator = $anonymousClassGenerator;
        $this->methodCallHandler = new BasicMethodCallHandler();
    }

    public function testItMustInstantiateAndReturnTheSameValueAsFirstArgument(): void
    {
        $obj = $this->instantiator->instantiate(
            InterfaceWithValidMethodSignature::class,
            $this->methodCallHandler
        );

        $value = 'foo';

        $response = $obj->test($value);

        self::assertEquals($response, $value);
    }
}