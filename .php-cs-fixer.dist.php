<?php

$header = <<<EOL
This file is part of The Waffler Project.

(c) Erick de Menezes <erickmenezes.dev@gmail.com>

This source file is subject to the MIT licence that is bundled
with this source code in the file LICENCE.
EOL;


$finder = PhpCsFixer\Finder::create()
    ->in([
        // bridges
        __DIR__ . '/libs/bridge/laravel-bridge/src',
        // components
        __DIR__ . '/libs/components/attributes/src',
        __DIR__ . '/libs/components/client/src',
        __DIR__ . '/libs/components/generator/src',
        __DIR__ . '/libs/components/helpers/src',
        // contracts
        __DIR__ . '/libs/contracts/attributes-contracts/src',
        __DIR__ . '/libs/contracts/client-contracts/src',
    ]);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PER-CS' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    'header_comment' => ['header' => $header],
])
    ->setFinder($finder);
