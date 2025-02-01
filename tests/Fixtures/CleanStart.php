<?php

namespace Waffler\Waffler\Tests\Fixtures;

use Waffler\Waffler\Client\Contracts\FactoryInterface;
use Waffler\Waffler\Client\Factory;
use Waffler\Waffler\Implementation\Factory\ClassFactory;
use Waffler\Waffler\Implementation\Factory\FileCacheFactory;
use Waffler\Waffler\Implementation\MethodValidator;
use Waffler\Waffler\Implementation\PathParser;

trait CleanStart
{
    private const IMPL_DIR = __DIR__.'/Impl';

    private FactoryInterface $factory;

    /**
     * @before
     */
    public function setUpFactoryAndFolder(): void
    {
        $this->cleanCacheFolder();
        $this->factory = new Factory(new FileCacheFactory(
            new ClassFactory(
                new MethodValidator(),
                new PathParser(),
            ),
            self::IMPL_DIR,
        ));
    }

    private function cleanCacheFolder(): void
    {
        $files = glob(self::IMPL_DIR.'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
