<?php

require_once 'vendor/autoload.php';

use League\ColorExtractor\ColorCounter\ImageIteratorColorCounter;
use League\ColorExtractor\ColorCounter\ImagickColorCounter;
use League\ColorExtractor\PixelIterator\GdIterator;
use League\ColorExtractor\PixelIterator\StandalonePngIterator;

class ColorCounterBench
{
    /**
     * @Revs(5)
     * @Iterations(2)
     */
    public function benchImagick()
    {
        new ImagickColorCounter(new Imagick(__DIR__.'/tests/assets/test.png'))->count();
    }

    /**
     * @Revs(5)
     * @Iterations(2)
     */
    public function benchGd()
    {
        new ImageIteratorColorCounter(new GdIterator(imagecreatefrompng(__DIR__.'/tests/assets/test.png')))->count();
    }

    /**
     * @Revs(5)
     * @Iterations(2)
     */
    public function benchNative()
    {
        new ImageIteratorColorCounter(new StandalonePngIterator(fopen(__DIR__.'/tests/assets/test.png', 'r')))->count();
    }
}
