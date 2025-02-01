<?php

namespace Waffler\Waffler\Tests\Fixtures;

use Waffler\Waffler\Client\Contracts\FactoryInterface;
use Waffler\Waffler\Client\Factory;

trait CleanStart
{
    private const IMPL_DIR = __DIR__.'/../../src/Impl';

    private FactoryInterface $factory;

    /**
     * @before
     */
    public function setUpFactoryAndFolder(): void
    {
        $this->cleanCacheFolder();
        $this->factory = new Factory();
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
