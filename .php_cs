<?php

$finder = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(array('src', 'tests'));

$config = Symfony\CS\Config\Config::create()
    ->finder($finder)
    ->level(\Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array('short_array_syntax'))
    ->setUsingCache(true);

return $config;