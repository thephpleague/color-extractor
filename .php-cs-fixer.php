<?php

$finder = (new PhpCsFixer\Finder())
    ->name('*.php')
    ->in(array('src', 'tests'))
;

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP7x4Migration' => true,
        '@PHP7x4Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,

        'phpdoc_to_comment' => ['ignored_tags' => ['var']],
    ])
;
