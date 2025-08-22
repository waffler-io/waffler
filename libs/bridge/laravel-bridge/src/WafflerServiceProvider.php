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

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Override;
use ReflectionException;
use Waffler\Bridge\Laravel\Commands\WafflerCacheCommand;
use Waffler\Bridge\Laravel\Commands\WafflerClearCommand;
use Waffler\Component\Client\Factory;
use Waffler\Contracts\Generator\Exceptions\ClassNotFoundExceptionInterface;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

/**
 * Class WafflerServiceProvider.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
final class WafflerServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(self::getPackageConfigPath(), 'waffler');
        $this->app->bind(Factory::class, static fn()
            => Factory::default()
            ->setHttpClientFactory(
                static fn(array $options) => new WafflerLaravelHttpClient($options),
            ));
        $this->app->bind(ClientListRetriever::class);
        $this->app->alias(ClientListRetriever::class, 'waffler.client-list-retriever');
        $this->app->alias(Factory::class, 'waffler.factory');
        $this->registerClients();
    }

    /**
     * @return non-empty-string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private static function getPackageConfigPath(): string
    {
        return __DIR__ . '/../config/waffler.php';
    }

    private function registerClients(): void
    {
        /**
         * @var array<string, array<string, mixed>> $sharedConfig
         */
        $sharedConfig = config('waffler.global_options', []);
        /**
         * @var array<class-string> $singletons
         */
        $singletons = config('waffler.singletons', []);
        $clientListRetriever = $this->app->make(ClientListRetriever::class);

        foreach ($clientListRetriever->clients as $clientInterface => $options) {
            $factory = fn(Application $app, array $args)
                => $app
                ->make(Factory::class)
                ->make(
                    $clientInterface,
                    // @phpstan-ignore-next-line
                    array_merge_recursive(
                        $sharedConfig,
                        $options,
                        $args[0] ?? [], // @phpstan-ignore-line
                    ),
                );

            $this->app->bind(
                $clientInterface,
                $factory,
                in_array($clientInterface, $singletons, true),
            );

            if ($alias = config('waffler.aliases.' . $clientInterface, false)) {
                if (is_string($alias)) {
                    $this->app->alias($clientInterface, $alias);
                }
            }
        }
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }
        $this->publishes([
            self::getPackageConfigPath() => config_path('waffler.php'),
        ], 'waffler-config');
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->optimizes(
            'waffler:cache',
            'waffler:clear',
        );
        $this->registerWafflerOptimizeCommand();
        $this->registerWafflerClearCommand();
        $this->commands([
            WafflerCacheCommand::class,
            WafflerClearCommand::class,
        ]);
    }

    private function registerWafflerOptimizeCommand(): void
    {
        $this->app->bind(WafflerCacheCommand::class, function (Application $app) {
            return new WafflerCacheCommand(
                $app['waffler.client-list-retriever'], // @phpstan-ignore-line
                $app['waffler.factory'], // @phpstan-ignore-line
            );
        });
    }

    private function registerWafflerClearCommand(): void
    {
        $this->app->bind(WafflerClearCommand::class, fn() => new WafflerClearCommand());
    }
}
