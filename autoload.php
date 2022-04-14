<?php

/*
 * This file is part of Waffler\Waffler.
 *
 * (c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>
 *
 * This source file is subject to the MIT licence that is bundled
 * with this source code in the file LICENCE.
 */

require __DIR__.'/vendor/autoload.php';

use ZEngine\Core;

if (ini_get('ffi.enable') === 'preload' && PHP_SAPI !== 'cli') {
    Core::preload();
} else {
    Core::init();
}
