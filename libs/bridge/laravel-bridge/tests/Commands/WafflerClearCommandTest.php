<?php

declare(strict_types=1);

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Bridge\Laravel\Tests\Commands;

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Waffler\Bridge\Laravel\Tests\TestCase;
use Waffler\Component\Generator\GeneratorDefaults;

#[Group('waffler/laravel-bridge')]
class WafflerClearCommandTest extends TestCase
{
    private string $tmpDir {
        get => $this->tmpDir ??= GeneratorDefaults::IMPL_CACHE_DIRECTORY;
    }

    private string $tmpFile {
        get => $this->tmpFile ??= "$this->tmpDir/test.php";
    }

    #[Before]
    public function setUpTestDir(): void
    {
        $files = glob("$this->tmpDir/*.php");
        if (!empty($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if (! is_dir($this->tmpDir)) {
            mkdir($this->tmpDir);
        }
        file_put_contents($this->tmpFile, '<?php ?>');
    }

    #[Test]
    public function it_must_clear_the_folder_returned_by_the_directory_resolver(): void
    {
        $this->assertFileExists($this->tmpFile);
        $this->artisan('waffler:clear');
        $this->assertFileDoesNotExist($this->tmpFile);
    }
}
