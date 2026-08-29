<?php

require_once 'vendor/autoload.php';

use League\ColorExtractor\PixelIterator\GdIterator;
use League\ColorExtractor\PixelIterator\ImagickIterator;
use League\ColorExtractor\PixelIterator\StandalonePngIterator;

class IteratorBench
{
    public function benchGd(): void
    {
        iterator_to_array(new GdIterator(imagecreatefrompng('tests/assets/test.png')));
    }

    public function benchImagick(): void
    {
        iterator_to_array(new ImagickIterator(new Imagick('tests/assets/test.png')));
    }

    public function benchPng(): void
    {
        iterator_to_array(new StandalonePngIterator(fopen('tests/assets/test.png', 'r')));
    }
}
