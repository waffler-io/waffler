<?php

namespace Waffler\Generator;

use Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class FactoryFunction.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @template TInterface
 */
class FactoryFunction
{
    /**
     * @psalm-param \Closure(MethodCallHandler): TInterface $factoryFunction
     */
    public function __construct(
        private \Closure $factoryFunction
    ) {
    }

    /**
     * @param MethodCallHandler $handler
     *
     * @return TInterface
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function __invoke(MethodCallHandler $handler): object
    {
        return ($this->factoryFunction)($handler);
    }
}