<?php

namespace Waffler\Generator;

use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class FactoryFunction.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template TInterfaceType of object
 */
class FactoryFunction
{
    /**
     * @phpstan-param \Closure(MethodCallHandler): TInterfaceType $factoryFunction
     */
    public function __construct(
        private \Closure $factoryFunction
    ) {
    }

    /**
     * @param MethodCallHandler $handler
     *
     * @return TInterfaceType
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function __invoke(MethodCallHandler $handler): object
    {
        return ($this->factoryFunction)($handler);
    }
}