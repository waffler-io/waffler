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

namespace Waffler\Bridge\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Trait UpdatesCachedConfigurationFile.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @mixin Command
 * @phpstan-require-extends Command
 */
trait UpdatesCachedConfigurationFile
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function updateCachedConfigurationFile(): void
    {
        if ($this->laravel instanceof CachesConfiguration && $this->laravel->configurationIsCached()) {
            /** @var array<string, mixed> $cachedContents */
            $cachedContents = require $this->laravel->getCachedConfigPath();
            $cachedContents['waffler-cache'] = config()->get('waffler-cache', []);
            file_put_contents(
                $this->laravel->getCachedConfigPath(),
                '<?php return ' . var_export($cachedContents, true) . ';' . PHP_EOL,
            );
        }
    }
}
