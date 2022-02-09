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
use ReflectionClass;
use Waffler\Waffler\Client\Contracts\FactoryInterface;
use Waffler\Waffler\Generator\AnonymousClassGenerator;

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
        return (new AnonymousClassGenerator())
            ->instantiate(new Proxy(
                new ReflectionClass($interfaceName),
                new MethodInvoker(new ResponseParser(), new Client($options)),
                $options
            ));
    }
}
