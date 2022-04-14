<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Client\Contracts;

/**
 * Interface ProxyInterface.
 *
 * This object handles all method calls of the anonymous classes.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @psalm-template TTargetedInterface of object
 * @mixin TTargetedInterface
 */
interface ProxyInterface
{
    /**
     * Handles all method calls.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function __call(string $name, array $arguments): mixed;
}
