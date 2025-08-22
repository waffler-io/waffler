<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Contracts;

use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Contracts\HttpClient\ClientInterface;

interface WafflerImplConstructorInterface
{
    /**
     * @param array<string, mixed> $options See {@see RequestOptions} for all available options.
     * @param FactoryInterface     $factory
     * @param ClientInterface      $client
     */
    public function __construct(
        array $options,
        FactoryInterface $factory,
        ClientInterface $client,
    );
}
