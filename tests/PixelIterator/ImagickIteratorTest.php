<?php

namespace League\ColorExtractor\Tests\PixelIterator;

use League\ColorExtractor\PixelIterator\ImagickIterator;

/**
 * @requires extension imagick
 */
class ImagickIteratorTest extends AbstractPixelIteratorTest
{
    protected function getIterator(string $imagePath): ImagickIterator
    {
        return new ImagickIterator(new \Imagick($imagePath));
    }
}
