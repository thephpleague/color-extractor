<?php

namespace League\ColorExtractor\ColorCounter;

use League\ColorExtractor\PixelIterator\PixelIteratorInterface;

class ImageIteratorColorCounter implements ColorCounterInterface
{
    public function __construct(private readonly PixelIteratorInterface $iterator) {}
    public function count(): array
    {
        $count = [];

        foreach ($this->iterator as $color) {
            isset($count[$color]) ? ++$count[$color] : $count[$color] = 1;
        }

        return $count;
    }
}
