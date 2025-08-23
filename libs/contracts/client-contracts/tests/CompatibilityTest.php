<?php

declare(strict_types = 1);

namespace Waffler\Contracts\Client\Tests;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Contracts\Client\PregeneratesClientsInterface;

#[Group('waffler/client-contracts')]
class CompatibilityTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testFactoryInterfaceCompatibility()
    {
        new class implements FactoryInterface {
            public function make(string $interface, array $options = []): object {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testPregeneratesClientsInterfaceCompatibility()
    {
        return new class implements PregeneratesClientsInterface {
            public function warmup(string $interface): void {}
        };
    }
}
