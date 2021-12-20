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

/**
 * Class AnonymousStage.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class AnonymousStage implements Contracts\StageInterface
{
    public function __construct(
        private Closure $callback
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(mixed $value): mixed
    {
        return ($this->callback)($value);
    }
}
