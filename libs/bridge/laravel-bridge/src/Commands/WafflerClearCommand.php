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
use Waffler\Component\Generator\GeneratorDefaults;

#[AsCommand(
    'waffler:clear',
    'Clear cache directory.',
)]
class WafflerClearCommand extends Command
{
    public function handle(): void
    {
        $directory = GeneratorDefaults::IMPL_CACHE_DIRECTORY;
        if (is_dir(GeneratorDefaults::IMPL_CACHE_DIRECTORY)) {
            $files = glob("$directory/*.php");
            if ($files !== false) {
                array_map(unlink(...), $files);
            }
        }
        $this->info('Waffler Cache cleared.');
    }
}
