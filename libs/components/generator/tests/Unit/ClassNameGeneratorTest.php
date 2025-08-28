<?php

declare(strict_types = 1);

namespace Waffler\Component\Generator\Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Waffler\Component\Generator\ClassNameGenerator;
use Waffler\Component\Generator\Tests\Fixtures\FixtureInterface;
use Waffler\Component\Generator\Tests\TestCase;

#[Group('waffler/generator')]
class ClassNameGeneratorTest extends TestCase
{
    #[Test]
    public function itMustGenerateTheClassNameForTheGivenInterface(): void
    {
        $generator = new ClassNameGenerator();
        $this->assertEquals(
            'Waffler_Component_Generator_Tests_Fixtures_FixtureInterface40cd750bba9870f18aada2478b24840aImpl',
            $generator->generateClassName(FixtureInterface::class),
        );
    }

    #[Test]
    public function itMustGenerateTheFullyQualifiedClassNameForTheGivenInterface(): void
    {
        $generator = new ClassNameGenerator('Tests');
        $this->assertEquals(
            'Tests\\Waffler_Component_Generator_Tests_Fixtures_FixtureInterface40cd750bba9870f18aada2478b24840aImpl',
            $generator->generateClassFqn(FixtureInterface::class),
        );
    }

    #[Test]
    public function performanceTestAgainstFileMd5(): void
    {
        $generator = new ClassNameGenerator();
        $fileStart = microtime(true);
        md5_file(__DIR__.'/../Fixtures/FixtureInterface.php');
        $fileEnd = microtime(true);

        $genStart = microtime(true);
        $generator->generateClassName(FixtureInterface::class);
        $genEnd = microtime(true);

        $this->assertLessThan($fileEnd - $fileStart, $genEnd - $genStart);
    }
}
