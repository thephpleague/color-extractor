<?php

namespace League\ColorExtractor\Test\Laravel;

use League\ColorExtractor\Laravel\ColorExtractorServiceProvider;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ColorExtractorFacadeTest extends TestCase
{
    public function testImages()
    {
        $image = \ColorExtractor::loadJpeg($this->jpegPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);

        $image = \ColorExtractor::loadGif($this->gifPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);

        $image = \ColorExtractor::loadPng($this->pngPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);
    }
}
