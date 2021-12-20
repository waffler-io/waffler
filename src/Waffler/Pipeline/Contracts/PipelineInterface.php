<?php

/*
 * This file is part of Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Pipeline\Contracts;

use Closure;

/**
 * Interface PipelineInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface PipelineInterface
{
    /**
     * Sets the initial value that will run through the pipeline.
     *
     * @param mixed $value
     *
     * @return $this
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function run(mixed $value): self;

    /**
     * Sets the Stages of the pipeline which the value will be passed.
     *
     * @param array<\Waffler\Pipeline\Contracts\StageInterface|Closure> $pipes
     *
     * @return $this
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function through(array $pipes): self;

    /**
     * Runs the pipeline and executes a final callback before return the value.
     *
     * @param \Closure $callback
     * @phpstan-param \Closure(mixed): mixed $callback
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function then(Closure $callback): mixed;

    /**
     * Runs the pipeline and returns the final value.
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function thenReturn(): mixed;
}
