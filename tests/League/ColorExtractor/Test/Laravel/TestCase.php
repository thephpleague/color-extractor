<?php

namespace League\ColorExtractor\Test\Laravel;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $jpegPath = './tests/assets/test.jpeg';
    protected $gifPath  = './tests/assets/test.gif';
    protected $pngPath  = './tests/assets/test.png';

    protected function getPackageProviders()
    {
        return array(
            'League\\ColorExtractor\\Laravel\\ColorExtractorServiceProvider',
        );
    }

    protected function getPackageAliases()
    {
        return array(
            'ColorExtractor' => 'League\\ColorExtractor\\Laravel\\ColorExtractorFacade',
        );
    }
}
