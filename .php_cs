<?php

$finder = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(array('src', 'tests'));

$config = Symfony\CS\Config\Config::create()
    ->finder($finder)
    ->setUsingCache(true);

return $config;