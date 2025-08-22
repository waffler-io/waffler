<?php

declare(strict_types=1);

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Repositories\Exceptions;

use RuntimeException;
use Throwable;
use Waffler\Contracts\Generator\Exceptions\ClassNotFoundExceptionInterface;

class ClassNotFoundException extends RuntimeException implements ClassNotFoundExceptionInterface
{
    public readonly string $interfaceFqn;

    public function __construct(string $interfaceFqn, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->interfaceFqn = $interfaceFqn;
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }

    public static function classDoesNotExists(string $interfaceFqn): self
    {
        return new self($interfaceFqn, sprintf('Class for interface %s not found.', $interfaceFqn));
    }
}
