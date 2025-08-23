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

namespace Waffler\Bridge\Laravel;

use Illuminate\Container\Attributes\Config;
use RuntimeException;

class ClientListRetriever
{
    /**
     * @var array<class-string, array<string, mixed>>
     */
    public protected(set) array $clients {
        get {
            return $this->clients ??= $this->normalizeClientsList();
        }
    }

    /**
     * @var array<class-string>
     */
    public protected(set) array $clientInterfaces {
        get {
            return $this->clientInterfaces ??= array_keys($this->clients);
        }
    }

    /**
     * @param array<class-string|int, class-string|array<string, mixed>> $clientsConfig
     */
    public function __construct(
        protected readonly array $clientsConfig,
    ) {}

    /**
     * @return array<class-string, array<string, mixed>>
     */
    protected function normalizeClientsList(): array
    {
        /** @var array<class-string, array<string, mixed>> $normalizedArray */
        $normalizedArray = [];

        foreach ($this->clientsConfig as $classStringOrIndex => $classStringOrOptions) {
            if (is_string($classStringOrIndex) && is_array($classStringOrOptions)) {
                $normalizedArray[$classStringOrIndex] = $classStringOrOptions;
            } elseif (is_string($classStringOrOptions)) {
                $normalizedArray[$classStringOrOptions] = [];
            } else {
                $this->throwInvalidConfigurationException();
            }
        }

        return $normalizedArray;
    }

    protected function throwInvalidConfigurationException(): never
    {
        throw new RuntimeException(
            "The waffler config file is invalid. The type of 'clients' must match array<class-string, array<string, mixed>>|array<class-string>",
        );
    }
}
