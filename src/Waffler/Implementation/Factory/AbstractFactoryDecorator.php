<?php

namespace Waffler\Waffler\Implementation\Factory;

abstract class AbstractFactoryDecorator implements FactoryInterface
{
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {}

    public function generateForInterface(string $interface): string
    {
        return $this->factory->generateForInterface($interface);
    }
}
