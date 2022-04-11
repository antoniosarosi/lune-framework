<?php

$finder = PhpCsFixer\Finder::create()->in([__DIR__."/src", __DIR__."/tests"]);

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same'
        ],
    ])
    ->setFinder($finder);
