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
use Symfony\Component\Console\Attribute\AsCommand;
use Waffler\Bridge\Laravel\DirectoryResolvers\DirectoryResolverInterface;
use Waffler\Component\Generator\Factory\FactoryDefaults;

#[AsCommand(
    'waffler:clear',
    'Clear cache directory.',
)]
class WafflerClearCommand extends Command
{
    public function handle(): void
    {
        $directory = FactoryDefaults::IMPL_CACHE_DIRECTORY;
        if (is_dir(FactoryDefaults::IMPL_CACHE_DIRECTORY)) {
            array_map(unlink(...), glob("$directory/*.php"));
        }
        $this->info('Waffler Cache cleared.');
    }
}
