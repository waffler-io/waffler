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

namespace Waffler\Waffler\Client;

use GuzzleHttp\Client;
use InvalidArgumentException;
use ReflectionClass;
use Waffler\Waffler\Client\Contracts\FactoryInterface;
use Waffler\Waffler\Client\Contracts\ProxyInterface;
use ZEngine\Reflection\ReflectionClass as ZEngineReflectionClass;

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
        if (!interface_exists($interfaceName)) {
            throw new InvalidArgumentException("Interface {$interfaceName} does not exist", 10);
        }
        return self::createProxy(
            new ReflectionClass($interfaceName),
            new MethodInvoker(new ResponseParser(), new Client($options)),
            $options
        );
    }

    /**
     * @param \ReflectionClass<TInterfaceType>      $interface
     * @param \Waffler\Waffler\Client\MethodInvoker $methodInvoker
     * @param array                                 $options
     *
     * @return ProxyInterface&TInterfaceType
     * @throws \ReflectionException
     * @author         ErickJMenezes <erickmenezes.dev@gmail.com>
     * @psalm-template TInterfaceType of object
     */
    private static function createProxy(
        ReflectionClass $interface,
        MethodInvoker $methodInvoker,
        array $options = []
    ): ProxyInterface {
        // We are creating an anonymous class here to preventing modifying the Proxy class with the low level
        // modifications.
        $proxy = new class ($interface, $methodInvoker, $options) extends Proxy {};

        // Here we add the given interface to the internal class implementation list inside zend engine. By doing this
        // we now can safely assign the anonymous Proxy object to a typed property or parameter with the same type as
        // the given interface.
        (new ZEngineReflectionClass($proxy))->addInterfaces($interface->getName());

        // Now the magic is done, now we just return the modified object.
        return $proxy;
    }
}
