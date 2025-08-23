<?php

declare(strict_types = 1);

namespace Waffler\Bridge\Laravel\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Waffler\Bridge\Laravel\ConfigCachedClassNameGenerator;
use Waffler\Bridge\Laravel\Tests\Fixtures\Interfaces\SimpleInterface;
use Waffler\Component\Generator\ClassNameGeneratorInterface;
use Waffler\Component\Generator\Contracts\WafflerImplConstructorInterface;

#[Group('waffler/laravel-bridge')]
class ConfigCachedClassNameGeneratorTest extends TestCase
{
    #[Test]
    public function itMustReturnTheClassNameIfItIsAlreadyCached(): void
    {
        $cachedClass = mock(SimpleInterface::class, WafflerImplConstructorInterface::class);
        $this->app['config']->set('waffler-cache.fqn', [
            SimpleInterface::class => $cachedClass::class,
        ]);
        $mock = mock(ClassNameGeneratorInterface::class);
        $mock
            ->shouldReceive('generateClassFqn')
            ->never();

        $generator = new ConfigCachedClassNameGenerator($mock);
        $result = $generator->generateClassFqn(SimpleInterface::class);
        $this->assertSame($cachedClass::class, $result);
    }

    #[Test]
    public function itMustGenerateTheClassNameIfItIsNotCached(): void
    {
        $this->app['config']->set('waffler-cache.fqn', []);
        $mock = mock(ClassNameGeneratorInterface::class);
        $mock
            ->shouldReceive('generateClassFqn')
            ->once()
            ->andReturn('cached!');
        $generator = new ConfigCachedClassNameGenerator($mock);
        $result = $generator->generateClassFqn(SimpleInterface::class);
        $this->assertSame('cached!', $result);
        $this->assertSame('cached!', $this->app['config']->get('waffler-cache.fqn')[SimpleInterface::class]);
    }

    #[Test]
    public function ifTheClassNameIsAlreadyCachedThenItMustReturnIt(): void
    {
        $this->app['config']->set('waffler-cache.class_name', [
            SimpleInterface::class => 'cached!',
        ]);
        $mock = mock(ClassNameGeneratorInterface::class);
        $mock
            ->shouldReceive('generateClassName')
            ->never();
        $generator = new ConfigCachedClassNameGenerator($mock);
        $result = $generator->generateClassName(SimpleInterface::class);
        $this->assertSame('cached!', $result);
        $this->assertSame('cached!', $this->app['config']->get('waffler-cache.class_name')[SimpleInterface::class]);
    }

    #[Test]
    public function ifTheClassNameIsNotCachedThenItMustGenerateIt(): void
    {
        $this->app['config']->set('waffler-cache.class_name', []);
        $mock = mock(ClassNameGeneratorInterface::class);
        $mock
            ->shouldReceive('generateClassName')
            ->once()
            ->andReturn('cached!');
        $generator = new ConfigCachedClassNameGenerator($mock);
        $result = $generator->generateClassName(SimpleInterface::class);
        $this->assertSame('cached!', $result);
        $this->assertSame('cached!', $this->app['config']->get('waffler-cache.class_name')[SimpleInterface::class]);
    }
}
