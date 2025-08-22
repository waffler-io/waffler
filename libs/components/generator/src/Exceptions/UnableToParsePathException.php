<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Generator\Exceptions;

use RuntimeException;
use Waffler\Contracts\Generator\Exceptions\GeneratorExceptionInterface;

/**
 * class UnableToParsePathException.
 *
 * This exception is thrown when is not possible to interpolate the path of the request.
 *
 * @author ErickJMenezes <erickmenezes.dev@gmail.com>
 */
class UnableToParsePathException extends RuntimeException implements GeneratorExceptionInterface
{
    //
}
