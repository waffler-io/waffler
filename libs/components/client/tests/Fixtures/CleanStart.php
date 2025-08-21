<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Client\Tests\Fixtures;

use PHPUnit\Framework\Attributes\Before;
use Waffler\Component\Client\Factory;
use Waffler\Component\Generator\Factory\FactoryDefaults;

trait CleanStart
{
    private Factory $factory;

    #[Before]
    public function setUpFactoryAndFolder(): void
    {
        $this->cleanCacheFolder();
        $this->factory = Factory::default();
    }

    private function cleanCacheFolder(): void
    {
        $files = glob(FactoryDefaults::IMPL_CACHE_DIRECTORY.'/*.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
