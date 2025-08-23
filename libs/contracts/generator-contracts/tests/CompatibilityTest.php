<?php

namespace Waffler\Contracts\Generator\Tests;

use Exception;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use Waffler\Contracts\Generator\ClassGeneratorInterface;
use Waffler\Contracts\Generator\ClassRepositoryInterface;
use Waffler\Contracts\Generator\DataTransferObjects\CachedClassInterface;
use Waffler\Contracts\Generator\Exceptions\ClassNotFoundExceptionInterface;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

#[Group('waffler/generator-contracts')]
class CompatibilityTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testClassRepositoryInterfaceCompatibility(): void
    {
        new class implements ClassRepositoryInterface {
            public function save(string $interfaceFqn, string $source): CachedClassInterface {}

            public function has(string $interfaceFqn): bool {}

            public function get(string $interfaceFqn): CachedClassInterface {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testClassGeneratorInterfaceCompatibility(): void
    {
        new class implements ClassGeneratorInterface {
            public function generateClass(string $interfaceFqn): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testClassNotFoundExceptionInterfaceCompatibility(): void
    {
        new class extends Exception implements ClassNotFoundExceptionInterface {
            public string $interfaceFqn;
        };
    }

    #[DoesNotPerformAssertions]
    public function testGeneratorExceptionInterfaceCompatibility(): void
    {
        new class extends Exception implements GeneratorExceptionInterface {};
    }


    #[DoesNotPerformAssertions]
    public function testCachedClassInterfaceCompatibility(): void
    {
        new class implements CachedClassInterface {
            public string $interfaceFqn;
            public string $classFqn;
        };
    }
}
