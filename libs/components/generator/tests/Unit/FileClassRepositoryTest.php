<?php

declare(strict_types = 1);

namespace Waffler\Component\Generator\Tests\Unit;

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Waffler\Component\Generator\FileClassRepository;
use Waffler\Component\Generator\Tests\Fixtures\FixtureInterface;
use Waffler\Component\Generator\Tests\TestCase;

#[Group('waffler/generator')]
class FileClassRepositoryTest extends TestCase
{
    private FileClassRepository $fileClassRepository;

    #[Test]
    public function ifTheFileDoesNotExistsItMustReturnFalse(): void
    {
        $this->assertFalse(
            $this->fileClassRepository->has(FixtureInterface::class),
        );
    }

    #[Test]
    public function ifTheFileExistsItMustReturnTrue(): void
    {
        $this->fileClassRepository->save(
            FixtureInterface::class,
            '<?php',
        );
        $this->assertTrue(
            $this->fileClassRepository->has(FixtureInterface::class),
        );
    }

    #[Test]
    public function ifTheFileExistsItMustReturnTheClass(): void
    {
        $this->fileClassRepository->save(
            FixtureInterface::class,
            '<?php',
        );
        $cachedClass = $this->fileClassRepository->get(FixtureInterface::class);
        $this->assertEquals(
            FixtureInterface::class,
            $cachedClass->interfaceFqn,
        );
        $this->assertEquals(
            'Tests\\Waffler_Component_Generator_Tests_Fixtures_FixtureInterface92d86230db36e0c2502e97342cda43f0Impl',
            $cachedClass->classFqn,
        );
    }

    #[Before]
    public function setUpRepository(): void
    {
        $dir = __DIR__.'/../Fixtures/Generated';
        foreach (glob($dir.'/*.php') as $file) {
            unlink($file);
        }

        $this->fileClassRepository = new FileClassRepository($dir, 'Tests');
    }
}
