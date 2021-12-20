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

/**
 * Interface StageInterface.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface StageInterface
{
    /**
     * Handle the value passed though the pipes.
     *
     * @param mixed                $value
     *
     * @return mixed
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    public function handle(mixed $value): mixed;
}
