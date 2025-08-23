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
use ReflectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Waffler\Bridge\Laravel\ClientListRetriever;
use Waffler\Contracts\Client\PregeneratesClientsInterface;
use Waffler\Contracts\Generator\Exceptions\ClassNotFoundExceptionInterface;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

#[AsCommand(
    'waffler:cache',
    'Warmup the cache directory with the generated classes.',
)]
class WafflerCacheCommand extends Command
{
    public function __construct(
        private readonly ClientListRetriever $clientListRetriever,
        private readonly PregeneratesClientsInterface $factory,
    ) {
        parent::__construct();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $this->withProgressBar(
            $this->clientListRetriever->clientInterfaces,
            $this->handleProgressBarCallback(...),
        );
        $this->updateCachedConfigurationFile();
        $this->info('Waffler classes has been generated.');
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function updateCachedConfigurationFile(): void
    {
        if ($this->laravel instanceof CachesConfiguration && $this->laravel->configurationIsCached()) {
            $contents = config()->get('waffler-cache', []);
            /** @var array<string, mixed> $cachedContents */
            $cachedContents = require $this->laravel->getCachedConfigPath();
            $cachedContents['waffler-cache'] = $contents;
            file_put_contents(
                $this->laravel->getCachedConfigPath(),
                '<?php return ' . var_export($cachedContents, true) . ';' . PHP_EOL,
            );
        }
    }

    /**
     * @param class-string $clientInterface
     * @param ProgressBar  $bar
     *
     * @throws ReflectionException
     * @throws ClassNotFoundExceptionInterface
     * @throws GeneratorExceptionInterface
     */
    private function handleProgressBarCallback(string $clientInterface, ProgressBar $bar): void
    {
        $bar->setMessage("Generating $clientInterface");
        $this->factory->warmup($clientInterface);
    }
}
