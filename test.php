<?php

use League\ColorExtractor\PixelIterator\GdIterator;
use League\ColorExtractor\PixelIterator\ImagickIterator;

include 'vendor/autoload.php';

var_dump(
    iterator_count(new ImagickIterator(new Imagick('tests/assets/google.png')))
);
