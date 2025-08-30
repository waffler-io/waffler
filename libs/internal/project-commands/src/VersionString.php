<?php

declare(strict_types = 1);

namespace Waffler\Internal\ProjectCommands;

use InvalidArgumentException;
use RuntimeException;
use Stringable;
use ValueError;

final class VersionString implements Stringable
{
    public string $value {
        get {
            return "{$this->major}.{$this->minor}.{$this->patch}";
        }
    }

    public function __construct(
        private(set) int|string $major {
            set {
                if (!is_numeric($value)) {
                    throw new ValueError('Major version must be numeric.');
                }
                $this->major = (int)$value;
            }
        },
        private(set) int|string $minor {
            set {
                if (!is_numeric($value)) {
                    throw new ValueError('Minor version must be numeric.');
                }
                $this->minor = (int)$value;
            }
        },
        private(set) int|string $patch {
            set {
                if (!is_numeric($value) && $value !== 'x-dev') {
                    throw new ValueError('Patch version must be numeric or x-dev.');
                }
                $this->patch = $value;
            }
        },
    ) {}

    public static function fromGit(): self
    {
        $tag = shell_exec('git describe --tags --abbrev=0');
        if (empty($tag)) {
            throw new RuntimeException('Could not get the current version.');
        }
        return self::fromString(rtrim($tag));
    }

    public static function fromString(string $raw): self
    {
        $parts = explode('.', $raw);
        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid version string.');
        }
        return new self(
            $parts[0],
            $parts[1],
            $parts[2],
        );
    }

    public function nextMajor(): self
    {
        $self = clone $this;
        $self->major = ((int)$this->major) + 1;
        $self->minor = 0;
        if (is_numeric($self->patch)) {
            $self->patch = 0;
        }
        return $self;
    }

    public function nextMinor(): self
    {
        $self = clone $this;
        $self->minor = ((int)$this->minor) + 1;
        if (is_numeric($self->patch)) {
            $self->patch = 0;
        }
        return $self;
    }

    public function nextPatch(): self
    {
        if (!is_numeric($this->patch)) {
            throw new RuntimeException('Patch version must be numeric.');
        }
        $self = clone $this;
        $self->patch = ((int)$this->patch) + 1;
        return $self;
    }

    public function asDevPatch(): self
    {
        $self = clone $this;
        $self->patch = 'x-dev';
        return $self;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
