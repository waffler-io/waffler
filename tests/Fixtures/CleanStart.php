<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Waffler\Tests\Fixtures;

use Waffler\Waffler\Client\Factory;

trait CleanStart
{
    private const string IMPL_DIR = __DIR__.'/../../generated';

    private Factory $factory;

    /**
     * @before
     */
    public function setUpFactoryAndFolder(): void
    {
        $this->cleanCacheFolder();
        $this->factory = Factory::default();
    }

    private function cleanCacheFolder(): void
    {
        $files = glob(self::IMPL_DIR.'/*.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
