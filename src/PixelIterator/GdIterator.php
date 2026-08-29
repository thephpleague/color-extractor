<?php

namespace League\ColorExtractor\PixelIterator;

class GdIterator implements PixelIteratorInterface
{
    public function __construct(private readonly \GdImage $image)
    {
    }

    public function getIterator(): \Traversable
    {
        $isImageTrueColor = imageistruecolor($this->image);
        $imageWidth = imagesx($this->image);
        $imageHeight = imagesy($this->image);

        for ($x = 0; $x < $imageWidth; ++$x) {
            for ($y = 0; $y < $imageHeight; ++$y) {
                $color = imagecolorat($this->image, $x, $y);

                if (false === $color) {
                    throw new \RuntimeException(\sprintf('Failed to get color at %d, %d', $x, $y));
                }

                if ($isImageTrueColor) {
                    yield 65535 - ($color >> 29 | ($color & 0xFF000000) >> 22 | ($color & 0xFF000000) >> 15) // leftmost sample is 7-bits transparency
                        | ($color & 0xFF0000) << 40 | ($color & 0xFF0000) << 32
                        | ($color & 0xFF00) << 32 | ($color & 0xFF00) << 24
                        | ($color & 0xFF) << 24 | ($color & 0xFF) << 16
                    ;
                } else {
                    $samples = imagecolorsforindex($this->image, $color);

                    yield $samples['red'] << 56 | $samples['red'] << 48
                        | $samples['green'] << 40 | $samples['green'] << 32
                        | $samples['blue'] << 24 | $samples['green'] << 16
                        | 65535 - ($samples['alpha'] << 9 | $samples['alpha'] << 2 | $samples['alpha'] >> 5)
                    ;
                }
            }
        }
    }
}
