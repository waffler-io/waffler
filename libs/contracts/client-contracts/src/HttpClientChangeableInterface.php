<?php

declare(strict_types=1);

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Contracts\Client;

use Closure;
use Waffler\Contracts\HttpClient\ClientInterface;

/**
 * Interface HttpClientChangeableInterface.
 *
 * Allows the user to use a custom http client factory.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-type FactoryClosure (Closure(array<string, mixed> $options): ClientInterface)
 */
interface HttpClientChangeableInterface
{
    /**
     * @param Closure                $closure
     *
     * @phpstan-param FactoryClosure $closure
     *
     * @return $this
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function setHttpClientFactory(Closure $closure): static;
}
