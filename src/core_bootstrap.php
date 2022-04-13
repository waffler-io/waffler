<?php

require_once __DIR__.'/../vendor/autoload.php';

use ZEngine\Core;

$reflectionCore = new ReflectionClass(Core::class);
$engine = $reflectionCore->getProperty('engine');
$engine->setAccessible(true);
if (!$engine->isInitialized()) {
    try {
        Core::preload();
    } catch (\FFI\Exception) {
        Core::init();
    }
}

