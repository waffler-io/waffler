<?php

namespace Waffler\Internal\ProjectCommands;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class BumpVersionCommand.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 * @phpstan-type ComposerFile array{
 *      require: array<string, string>,
 *      extra: array{
 *          branch-alias: array{
 *              dev-main: string,
 *              dev-master: string,
 *          },
 *      },
 *  }
 */
#[AsCommand(
    name: 'waffler:bump-version',
    description: 'Bump the version of the project.',
)]
final class BumpVersionCommand extends Command
{
    private const string BUGFIX = 'bugfix';
    private const string MINOR = 'minor';
    private const string MAJOR = 'major';

    private VersionString $currentVersion {
        get => $this->currentVersion ??= VersionString::fromGit();
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
        [$newVersion, $devVersion] = $this->getNewVersion(
            $input->getArgument('new_version'), //@phpstan-ignore-line
        );
        $output->writeln([
            "Current tagged version is: {$this->currentVersion}",
            "Bumping new version {$newVersion}",
        ], OutputInterface::OUTPUT_NORMAL);
        $composerFiles = $this->getComposerFiles();
        foreach ($composerFiles as $composerFilePath) {
            $output->writeln("");
            $output->writeln(
                "Editing file {$composerFilePath}...",
                OutputInterface::OUTPUT_NORMAL,
            );
            $composerFileContents = file_get_contents($composerFilePath);
            if ($composerFileContents === false) {
                throw new RuntimeException("Could not get the contents of file {$composerFilePath}");
            }
            /**
             * @var ComposerFile $composerConfig
             */
            $composerConfig = json_decode($composerFileContents, true);
            foreach ($composerConfig['require'] as $package => $version) {
                if (str_starts_with($package, 'waffler/')) {
                    $currentVersion = $composerConfig['require'][$package];
                    $composerConfig['require'][$package] = "^$newVersion";
                    $output->writeln(
                        "Bumping {$package} from {$currentVersion} to {$composerConfig['require'][$package]}",
                        OutputInterface::OUTPUT_NORMAL,
                    );
                }
            }
            $composerConfig['extra']['branch-alias']['dev-main'] = $devVersion;
            $composerConfig['extra']['branch-alias']['dev-master'] = $devVersion;
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
                    $composerFilePath,
                    $this->toJson($composerConfig),
                );
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @return array{VersionString, VersionString}
     */
    private function getNewVersion(string $kind): array
    {
        $version = match ($kind) {
            self::MAJOR => $this->currentVersion->nextMajor(),
            self::MINOR => $this->currentVersion->nextMinor(),
            self::BUGFIX => $this->currentVersion->nextPatch(),
            default => throw new InvalidArgumentException('Invalid version kind.'),
        };
        return [
            $version,
            $version->asDevPatch(),
        ];
    }

    /**
     * @return array<string>
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function getComposerFiles(): array
    {
        $basePath = realpath(__DIR__.'/../../../../');
        if ($basePath === false) {
            throw new RuntimeException('Could not get the base path.');
        }
        $paths = [
            'libs/bridge/*/composer.json',
            'libs/components/*/composer.json',
            'libs/contracts/*/composer.json',
            'composer.json',
        ];
        $files = [];
        foreach ($paths as $path) {
            $foundComposerFiles = glob($basePath.'/'.$path);
            if ($foundComposerFiles === false) {
                throw new RuntimeException("Could not find composer files in path {$basePath}/{$path}");
            }
            array_push($files, ...$foundComposerFiles);
        }
        return $files;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function toJson(array $data): string
    {
        return (string)preg_replace_callback(
            '/^(?: {4})+/m',
            fn($m) => str_repeat("  ", (int)(strlen($m[0]) / 4)),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }
}
