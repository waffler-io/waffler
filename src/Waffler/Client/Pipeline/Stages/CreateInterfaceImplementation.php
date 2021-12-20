<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client\Pipeline\Stages;

use Waffler\Generator\AnonymousClassGenerator;
use Waffler\Pipeline\Contracts\StageInterface;

/**
 * Class CreateInterfaceImplementation.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class CreateInterfaceImplementation implements StageInterface
{
    /**
     * @param \Waffler\Client\Proxy<TInterface> $value
     *
     * @return TInterface
     * @template TInterface of object
     * @throws \ReflectionException
     */
    public function handle(mixed $value): object
    {
        return (new AnonymousClassGenerator())->instantiate($value);
    }
}
