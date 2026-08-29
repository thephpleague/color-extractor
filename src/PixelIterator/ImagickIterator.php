<?php

namespace League\ColorExtractor\PixelIterator;

class ImagickIterator implements PixelIteratorInterface
{
    public function __construct(private readonly \Imagick $image)
    {
    }

    public function getIterator(): \Traversable
    {
        if (1 !== $this->image->count()) {
            $this->image->rewind();
        }

        ['quantumDepthLong' => $sampleDepth] = \Imagick::getQuantumDepth();

        foreach ($this->image->getPixelIterator() as $row) {
            /** @var \ImagickPixel $pixel */
            foreach ($row as $pixel) {
                ['r' => $r, 'g' => $g, 'b' => $b, 'a' => $a] = $pixel->getColorQuantum();

                if (16 === $sampleDepth) {
                    yield $r << 48 | $g << 32 | $b << 16 | $a;
                } elseif (16 > $sampleDepth) {

                } else {

                }
            }
        }
    }
}
