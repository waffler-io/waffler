<?php

declare(strict_types = 1);

namespace Waffler\Contracts\Attributes\Tests;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use Waffler\Contracts\Attributes\ArraySettable;
use Waffler\Contracts\Attributes\KeyedAttribute;
use Waffler\Contracts\Attributes\Pathable;
use Waffler\Contracts\Attributes\Verb;

#[Group('waffler/attributes-contracts')]
class CompatibilityTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testArraySettableCompatibility(): void
    {
        new class implements ArraySettable {
            public function getPathSeparator(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testKeyedAttributeCompatibility(): void
    {
        new class implements KeyedAttribute {
            public function getKey(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testPathableCompatibility(): void
    {
        new class implements Pathable {
            public function getPath(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testVerbCompatibility(): void
    {
        new class implements Verb {
            public function getPath(): string {}

            public function getName(): string {}
        };
    }
}
