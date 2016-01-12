<?php

namespace League\ColorExtractor\Test;

use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class PaletteTest extends \PHPUnit_Framework_TestCase
{
    protected $jpegPath = './tests/assets/test.jpeg';
    protected $gifPath = './tests/assets/test.gif';
    protected $pngPath = './tests/assets/test.png';

    public function testJpegExtractSingleColor()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->jpegPath));
        $colors = $extractor->extract(1);

        $this->assertInternalType('array', $colors);
        $this->assertCount(1, $colors);
        $this->assertEquals(15985688, $colors[0]);
    }

    public function testGifExtractSingleColor()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->gifPath));
        $colors = $extractor->extract(1);

        $this->assertInternalType('array', $colors);
        $this->assertCount(1, $colors);
        $this->assertEquals(12022491, $colors[0]);
    }

    public function testPngExtractSingleColor()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->pngPath));
        $colors = $extractor->extract(1);

        $this->assertInternalType('array', $colors);
        $this->assertCount(1, $colors);
        $this->assertEquals(14024704, $colors[0]);
    }

    public function testJpegExtractMultipleColors()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->pngPath));
        $numColors = 3;
        $colors = $extractor->extract($numColors);

        $this->assertInternalType('array', $colors);
        $this->assertCount($numColors, $colors);
        $this->assertEquals($colors, [14024704, 3407872, 15118468]);
    }
}
