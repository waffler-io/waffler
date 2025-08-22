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

namespace Waffler\Contracts\Generator\Exceptions;

use Throwable;

/**
 * Interface GeneratorExceptionInterface.
 *
 * Base exception for all exceptions thrown by the generator.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
interface GeneratorExceptionInterface extends Throwable {}
