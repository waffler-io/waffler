<?php

declare(strict_types=1);

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Client;

use ReflectionClass;
use Waffler\Client\Contracts\FactoryInterface;
use Waffler\Client\Pipeline\Stages\CreateInterfaceImplementation;
use Waffler\Client\Pipeline\Stages\CreateMethodCallProxy;
use Waffler\Client\Pipeline\Stages\EnsureReflectionClassIsFromAnInterface;
use Waffler\Pipeline\Pipeline;

/**
 * Class Client
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class Factory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function make(string $interfaceName, array $options = []): object
    {
        // The process of instantiating the given interface name is abstracted in the pipeline below.

        return (new Pipeline())
            ->run(new ReflectionClass($interfaceName))
            ->through([
                new EnsureReflectionClassIsFromAnInterface(),
                new CreateMethodCallProxy($options),
                new CreateInterfaceImplementation()
            ])
            ->thenReturn();
    }
}
