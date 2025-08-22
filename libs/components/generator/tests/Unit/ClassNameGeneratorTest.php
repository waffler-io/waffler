<?php

declare(strict_types=1);

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
            'Waffler_Component_Generator_Tests_Fixtures_FixtureInterface92d86230db36e0c2502e97342cda43f0Impl',
            $generator->generateClassName(FixtureInterface::class),
        );
    }

    #[Test]
    public function itMustGenerateTheFullyQualifiedClassNameForTheGivenInterface(): void
    {
        $generator = new ClassNameGenerator('Tests');
        $this->assertEquals(
            'Tests\\Waffler_Component_Generator_Tests_Fixtures_FixtureInterface92d86230db36e0c2502e97342cda43f0Impl',
            $generator->generateClassFqn(FixtureInterface::class),
        );
    }
}
