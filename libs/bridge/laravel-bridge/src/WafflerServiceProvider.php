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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Override;
use Waffler\Bridge\Laravel\Commands\WafflerCacheCommand;
use Waffler\Bridge\Laravel\Commands\WafflerClearCommand;
use Waffler\Component\Client\Factory;
use Waffler\Component\Generator\ClassGenerator;
use Waffler\Component\Generator\ClassNameGenerator;
use Waffler\Component\Generator\FileClassRepository;
use Waffler\Component\Generator\MethodValidator;
use Waffler\Component\Generator\PathParser;

/**
 * Class WafflerServiceProvider.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
final class WafflerServiceProvider extends ServiceProvider
{
    private const string PACKAGE_MAIN_CONFIG_PATH = __DIR__ . '/../config/waffler.php';
    private const string PACKAGE_CACHE_CONFIG_PATH = __DIR__ . '/../config/waffler-cache.php';

    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(self::PACKAGE_MAIN_CONFIG_PATH, 'waffler');
        $this->mergeConfigFrom(self::PACKAGE_CACHE_CONFIG_PATH, 'waffler-cache');
        $this->registerFactorySingleton();
        $this->app->bind(ClientListRetriever::class, function (Application $app) {
            return new ClientListRetriever(
                // @phpstan-ignore-next-line
                $app['config']->get('waffler.clients', []),
            );
        });
        $this->registerClients();
    }

    private function registerFactorySingleton(): void
    {
        $this->app->singleton(Factory::class, static function () {
            return new Factory(
                new FileClassRepository(
                    new ConfigCachedClassNameGenerator(
                        new ClassNameGenerator(),
                    ),
                ),
                new ClassGenerator(
                    new MethodValidator(),
                    new PathParser(),
                ),
            )
                ->setHttpClientFactory(
                    static fn(array $options) => new WafflerLaravelHttpClient($options),
                );
        });
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
            self::PACKAGE_MAIN_CONFIG_PATH => config_path('waffler.php'),
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
                $app->make(ClientListRetriever::class),
                $app->make(Factory::class),
            );
        });
    }

    private function registerWafflerClearCommand(): void
    {
        $this->app->bind(WafflerClearCommand::class, fn() => new WafflerClearCommand());
    }
}
