<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Utils;

use Attribute;

/**
 * Class RawOptions.
 *
 * Options to be merged with GuzzleHTTP Client request options.
 *
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class RawOptions {}
