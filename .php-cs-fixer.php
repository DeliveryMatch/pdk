<?php


use PhpCsFixer\Config;

return (new Config())
	->setCacheFile(__DIR__ . '/.cache/php-cs-fixer/.php-cs-fixer.cache') // Change this to your desired cache file location
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . "/src")
    );
