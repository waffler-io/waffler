<?php

declare(strict_types=1);

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Bridge\Laravel\Tests\Commands;

use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Waffler\Bridge\Laravel\Tests\Fixtures\Interfaces\FooClientInterface;
use Waffler\Bridge\Laravel\Tests\TestCase;
use Waffler\Component\Generator\GeneratorDefaults;

#[Group('waffler/laravel-bridge')]
class WafflerCacheCommandTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('waffler.clients', [
            FooClientInterface::class => ['base_uri' => 'localhost']
        ]);
    }

    #[Test]
    public function it_must_pre_generate_all_interfaces_inside_vendor_folder(): void
    {
        $cacheDirectory = GeneratorDefaults::IMPL_CACHE_DIRECTORY;
        //
        $this->assertArrayNotHasKey(FooClientInterface::class, config('waffler-cache.fqn'));
        $this->artisan('waffler:clear')->assertSuccessful();
        $result = glob("{$cacheDirectory}/*FooClientInterface*.php");
        $this->assertCount(0, $result);
        //
        $this->artisan('waffler:cache')->assertSuccessful();
        $result = glob("{$cacheDirectory}/*FooClientInterface*.php");
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(FooClientInterface::class, config('waffler-cache.fqn'));
    }
}
