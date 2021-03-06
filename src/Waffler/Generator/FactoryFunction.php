<?php

declare(strict_types=1);

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Generator;

use Closure;
use Waffler\Waffler\Generator\Contracts\MethodCallHandler;

/**
 * Class FactoryFunction.
 *
 * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
 * @psalm-template TInterfaceType of object
 */
class FactoryFunction
{
    /**
     * @param \Closure(MethodCallHandler<TInterfaceType>): TInterfaceType $factoryFunction
     */
    public function __construct(
        private Closure $factoryFunction
    ) {
    }

    /**
     * @param MethodCallHandler<TInterfaceType> $handler
     *
     * @return TInterfaceType
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function __invoke(MethodCallHandler $handler): object
    {
        return ($this->factoryFunction)($handler);
    }
}
