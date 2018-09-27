<?php

namespace LeagueTest\ColorExtractor;

use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

class PaletteTest extends TestCase
{
    protected $jpegPath = __DIR__.'/assets/test.jpeg';
    protected $gifPath = __DIR__.'/assets/test.gif';
    protected $pngPath = __DIR__.'/assets/test.png';
    protected $transparentPngPath = __DIR__.'/assets/red-transparent-50.png';

    public function testJpegExtractSingleColor()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->jpegPath));
        $colors = $extractor->extract(1);

        self::assertInternalType('array', $colors);
        self::assertCount(1, $colors);
        self::assertEquals(15985688, $colors[0]);
    }

    public function testGifExtractSingleColor()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->gifPath));
        $colors = $extractor->extract(1);

        self::assertInternalType('array', $colors);
        self::assertCount(1, $colors);
        self::assertEquals(12022491, $colors[0]);
    }

    public function testPngExtractSingleColor()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->pngPath));
        $colors = $extractor->extract(1);

        self::assertInternalType('array', $colors);
        self::assertCount(1, $colors);
        self::assertEquals(14024704, $colors[0]);
    }

    public function testJpegExtractMultipleColors()
    {
        $extractor = new ColorExtractor(Palette::fromFilename($this->pngPath));
        $numColors = 3;
        $colors = $extractor->extract($numColors);

        self::assertInternalType('array', $colors);
        self::assertCount($numColors, $colors);
        self::assertEquals($colors, [14024704, 3407872, 7111569]);
    }

    public function testTransparencyHandling()
    {
        self::assertCount(0, Palette::fromFilename($this->transparentPngPath));

        $whiteBackgroundPalette = Palette::fromFilename($this->transparentPngPath, Color::fromHexToInt('#FFFFFF'));
        self::assertEquals(iterator_to_array($whiteBackgroundPalette), [Color::fromHexToInt('#FF8080') => 1]);

        $blackBackgroundPalette = Palette::fromFilename($this->transparentPngPath, Color::fromHexToInt('#000000'));
        self::assertEquals(iterator_to_array($blackBackgroundPalette), [Color::fromHexToInt('#7E0000') => 1]);
    }
}
