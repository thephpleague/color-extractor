<?php

namespace League\ColorExtractor\Tests\PixelIterator;

use League\ColorExtractor\PixelIterator\PixelIteratorInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractPixelIteratorTest extends TestCase
{
    /**
     * @dataProvider provideWhitePixelImage
     */
    public function testPixelFormats(string $imageName): void
    {
        $this->assertSame(
            [-1],
            iterator_to_array($this->getIterator(__DIR__.'/fixtures/'.$imageName)),
        );
    }

    public static function provideWhitePixelImage(): iterable
    {
        yield '8-bits greyscale' => ['8bits-greyscale-white.png'];
        yield '8-bits greyscale with alpha' => ['8bits-greyscale-alpha-white.png'];

        yield '16-bits greyscale' => ['16bits-greyscale-white.png'];
        yield '16-bits greyscale with alpha' => ['16bits-greyscale-alpha-white.png'];

        yield '1-bit palette' => ['1bit-palette-white.png'];

        yield '8-bits RGB' => ['8bits-rgb-white.png'];
        yield '8-bits RGBA' => ['8bits-rgba-white.png'];

        yield '16-bits RGB' => ['16bits-rgb-white.png'];
        yield '16-bits RGBA' => ['16bits-rgba-white.png'];
    }

    abstract protected function getIterator(string $imagePath): PixelIteratorInterface;
}
