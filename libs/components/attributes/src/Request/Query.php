<?php

/*
 * This file is part of The Waffler Project.
 *
 * (c) Erick de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

namespace Waffler\Component\Attributes\Request;

use Attribute;

/**
 * Class QueryParams
 *
 * A key-value array to be used as query strings.
 *
 * @see     \GuzzleHttp\RequestOptions::QUERY
 * @author  ErickJMenezes <erickmenezes.dev@gmail.com>
 * @package Waffler\Component\Attributes\Request
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Query {}
