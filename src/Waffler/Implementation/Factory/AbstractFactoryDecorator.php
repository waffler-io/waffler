<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Implementation\Factory;

abstract class AbstractFactoryDecorator implements FactoryInterface
{
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {
    }

    public function generateForInterface(string $interface): string
    {
        return $this->factory->generateForInterface($interface);
    }
}
