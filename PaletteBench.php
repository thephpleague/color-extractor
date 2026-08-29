<?php

require_once 'vendor/autoload.php';

use League\ColorExtractor\Palette;

class PaletteBench
{
    public function benchImagickHistogram(): void
    {
        Palette::fromImagick(new Imagick('tests/assets/google.png'));
    }

    public function benchImagickPixelIterator(): void
    {
        Palette::fromImagickPixelIterator(new Imagick('tests/assets/google.png'));
    }
}
