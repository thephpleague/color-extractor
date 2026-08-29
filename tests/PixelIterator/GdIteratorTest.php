<?php

namespace League\ColorExtractor\Tests\PixelIterator;

use League\ColorExtractor\PixelIterator\GdIterator;

/**
 * @requires extension gd
 */
class GdIteratorTest extends AbstractPixelIteratorTest
{
    protected function getIterator(string $imagePath): GdIterator
    {
        return new GdIterator(imagecreatefromstring(file_get_contents($imagePath)));
    }
}
