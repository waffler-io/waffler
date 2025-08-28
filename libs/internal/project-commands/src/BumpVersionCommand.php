<?php

namespace Waffler\Internal\ProjectCommands;

use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'waffler:bump-version',
    description: 'Bump the version of the project.',
)]
final class BumpVersionCommand extends Command
{
    private const string BUGFIX = 'bugfix';
    private const string MINOR = 'minor';
    private const string MAJOR = 'major';

    private string $currentVersion {
        get {
            if (isset($this->currentVersion)) {
                return $this->currentVersion;
            }
            $tag = shell_exec('git describe --tags --abbrev=0');
            if (!$tag) {
                throw new \RuntimeException('Could not get the current version.');
            }
            $this->currentVersion = rtrim($tag);
            return $tag;
        }
    }

    protected function configure(): void
    {
        $this->setDefinition([
            new InputArgument(
                'new_version',
                InputArgument::OPTIONAL,
                'The new version to be bumped.',
                self::BUGFIX,
                [
                    self::BUGFIX,
                    self::MINOR,
                    self::MAJOR,
                ],
            ),
            new InputOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Dry run.',
            ),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            "Fetching latest tags from remote repository...",
            OutputInterface::OUTPUT_NORMAL,
        );
        shell_exec('git fetch --tags');
        $output->writeln("Done.", OutputInterface::OUTPUT_NORMAL);
        $dryRun = $input->getOption('dry-run');
        [$newVersion, $devVersion] = $this->getNewVersion($input->getArgument('new_version'));
        $output->writeln([
            "Current tagged version is: {$this->currentVersion}",
            "Bumping new version {$newVersion}",
        ], OutputInterface::OUTPUT_NORMAL);
        $composerFiles = [
            ...glob(__DIR__.'/../../../bridge/*/composer.json'),
            ...glob(__DIR__.'/../../../components/*/composer.json'),
            ...glob(__DIR__.'/../../../contracts/*/composer.json'),
            ...glob(__DIR__.'/../../../../composer.json'),
        ];
        foreach ($composerFiles as $composerFile) {
            $output->writeln("");
            $output->writeln(
                "Editing file {$composerFile}...",
                OutputInterface::OUTPUT_NORMAL,
            );
            $composer = json_decode(file_get_contents($composerFile), true);
            foreach ($composer['require'] as $package => $version) {
                if (str_starts_with($package, 'waffler/')) {
                    $currentVersion = $composer['require'][$package];
                    $composer['require'][$package] = "^$newVersion";
                    $output->writeln(
                        "Bumping {$package} from {$currentVersion} to {$composer['require'][$package]}",
                        OutputInterface::OUTPUT_NORMAL,
                    );
                }
            }
            $composer['extra']['branch-alias']['dev-main'] = $devVersion;
            $composer['extra']['branch-alias']['dev-master'] = $devVersion;
            $output->writeln(
                "Set branch alias to {$devVersion}",
                OutputInterface::OUTPUT_NORMAL,
            );
            if ($dryRun) {
                $output->writeln(
                    "Dry run. Changes will have no effect to this file.",
                    OutputInterface::OUTPUT_NORMAL,
                );
            } else {
                file_put_contents(
                    $composerFile,
                    $this->encode($composer),
                );
            }
        }

        return Command::SUCCESS;
    }

    private function getNewVersion(string $kind): array
    {
        [$major, $minor, $patch] = explode('.', $this->currentVersion);
        $parts = match ($kind) {
            self::MAJOR => [(int)$major + 1, 0, 0],
            self::MINOR => [$major, (int)$minor + 1, 0],
            self::BUGFIX => [$major, $minor, (int)$patch + 1],
            default => throw new InvalidArgumentException('Invalid version kind.'),
        };
        return [
            implode('.', $parts),
            implode('.', [$parts[0], $parts[1], 'x-dev']),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function encode(array $data): string
    {
        return preg_replace_callback(
            '/^(?: {4})+/m',
            fn($m) => str_repeat("  ", strlen($m[0]) / 4),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }
}
