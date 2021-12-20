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

use GuzzleHttp\Client;
use Waffler\Client\MethodInvoker;
use Waffler\Client\Proxy;
use Waffler\Client\ResponseParser;
use Waffler\Pipeline\Contracts\StageInterface;

/**
 * Class CreateMethodCallProxy.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class CreateMethodCallProxy implements StageInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private array $options
    ) {
    }

    /**
     * @param \ReflectionClass<T> $value
     *
     * @return Proxy<T>
     * @author   ErickJMenezes <erickmenezes.dev@gmail.com>
     * @template T of object
     */
    public function handle(mixed $value): Proxy
    {
        return new Proxy($value, $this->newMethodInvoker());
    }

    /**
     * @return \Waffler\Client\MethodInvoker
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    protected function newMethodInvoker(): MethodInvoker
    {
        return new MethodInvoker(new ResponseParser(), new Client($this->options));
    }
}
