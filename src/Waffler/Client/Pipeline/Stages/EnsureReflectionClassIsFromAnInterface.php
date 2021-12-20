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

use InvalidArgumentException;
use Waffler\Pipeline\Contracts\StageInterface;

/**
 * Class ValidateInterfaceAndOptions.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class EnsureReflectionClassIsFromAnInterface implements StageInterface
{
    /**
     * @param \ReflectionClass<T> $value
     *
     * @return \ReflectionClass<T>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T of object
     */
    public function handle(mixed $value): mixed
    {
        if (!$value->isInterface()) {
            throw new InvalidArgumentException(
                "The value \"{$value->getName()}\" is not a valid fully qualified interface name.",
                10
            );
        }

        return $value;
    }
}
