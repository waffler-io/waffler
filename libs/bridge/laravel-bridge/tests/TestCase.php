<?php

declare(strict_types=1);

namespace Waffler\Bridge\Laravel\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Override;
use PHPUnit\Framework\Attributes\Group;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Waffler\Bridge\Laravel\WafflerServiceProvider;

#[Group('waffler/laravel-bridge')]
class TestCase extends BaseTestCase
{
    use WithWorkbench;

    #[Override]
    protected function getPackageProviders($app): array
    {
        return [
            WafflerServiceProvider::class,
        ];
    }
}
