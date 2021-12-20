<?php

$header = <<<EOL
This file is part of Waffler.

(c) Erick Johnson Almeida de Menezes <erickmenezes.dev@gmail.com>

This source file is subject to the MIT licence that is bundled
with this source code in the file LICENCE.
EOL;


$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'vendor',
        '.phpunit.cache'
    ])
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    'header_comment' => ['header' => $header],
])
    ->setFinder($finder);