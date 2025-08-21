<?php

declare(strict_types=1);

namespace Waffler\Contracts\Client\Tests;

use Waffler\Contracts\Client\FactoryInterface;
use Waffler\Contracts\Client\PregeneratesClientsInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;

#[Group('waffler/client-contracts')]
class CompatibilityTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testFactoryInterfaceCompatibility()
    {
        new class implements FactoryInterface
        {
            public function make(string $interface, array $options = []): object
            {
                return new \stdClass();
            }
        };
    }

    #[DoesNotPerformAssertions]
    public function testPregeneratesClientsInterfaceCompatibility()
    {
        return new class implements PregeneratesClientsInterface
        {
            public function warmup(string $interface): void
            {
            }
        };
    }
}
