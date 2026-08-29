<?php

namespace League\ColorExtractor\ColorCounter;

use League\ColorExtractor\Color;
use League\ColorExtractor\Sample;

class ImagickColorCounter implements ColorCounterInterface
{
    public function __construct(private readonly \Imagick $image) {}
    public function count(): array
    {
        $count = [];

        if (1 !== $this->image->count()) {
            $this->image->rewind();
        }

        /** @var \ImagickPixel $pixel */
        foreach ($this->image->getImageHistogram() as $pixel) {
            $samples = $pixel->getColor();
            $count[($samples['r'] << 40 | $samples['r'] << 32) + ($samples['g'] << 24 | $samples['g'] << 16) + ($samples['b'] << 8 | $samples['b'])] = $pixel->getColorCount();
        }

        return $count;
    }
}
