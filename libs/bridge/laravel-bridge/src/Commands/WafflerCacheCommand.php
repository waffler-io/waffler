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
use ReflectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Waffler\Bridge\Laravel\ClientListRetriever;
use Waffler\Contracts\Client\PregeneratesClientsInterface;

#[AsCommand(
    'waffler:cache',
    'Warmup the cache directory with the generated classes.',
)]
class WafflerCacheCommand extends Command
{
    public function __construct(
        private readonly ClientListRetriever $clientListRetriever,
        private readonly PregeneratesClientsInterface $factory,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->withProgressBar(
            $this->clientListRetriever->clientInterfaces,
            /**
             * @param class-string $clientInterface
             *
             * @throws ReflectionException
             */
            function (string $clientInterface, ProgressBar $bar): void {
                $bar->setMessage("Generating $clientInterface");
                $this->factory->warmup($clientInterface);
            },
        );
        $this->info('Waffler classes has been generated.');
    }
}
