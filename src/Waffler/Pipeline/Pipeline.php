<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Pipeline;

use Closure;
use InvalidArgumentException;
use Waffler\Pipeline\Contracts\PipelineInterface;
use Waffler\Pipeline\Contracts\StageInterface;

/**
 * Class Pipeline.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class Pipeline implements PipelineInterface, StageInterface
{
    private mixed $carry = null;

    /**
     * @var array<\Waffler\Pipeline\Contracts\StageInterface>
     */
    private array $pipes = [];

    public function run(mixed $value): PipelineInterface
    {
        $this->carry = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function through(array $pipes): PipelineInterface
    {
        foreach ($pipes as $pipe) {
            if ($pipe instanceof StageInterface) {
                $this->pipes[] = $pipe;
            } elseif ($pipe instanceof Closure) {
                $this->pipes[] = new AnonymousStage($pipe);
            } else {
                throw new InvalidArgumentException("Invalid array value.");
            }
        }

        return $this;
    }

    public function then(Closure $callback): mixed
    {
        $this->execute();

        return $callback($this->carry);
    }

    public function thenReturn(): mixed
    {
        return $this->then(fn (mixed $finalValue) => $finalValue);
    }

    public function handle(mixed $value): mixed
    {
        return $this->run($value)->thenReturn();
    }

    private function execute(): void
    {
        foreach ($this->pipes as $pipe) {
            $this->carry = $pipe->handle($this->carry);
        }
    }
}
