<?php

declare(strict_types=1);

namespace League\ColorExtractor\Tests;

use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use PHPUnit\Framework\TestCase;

class PaletteTest extends TestCase
{
    public function testTransparencyHandling(): void
    {
        $imagePath = __DIR__.'/assets/red-transparent-50.png';
        $this->assertCount(0, Palette::fromFilename($imagePath));

        $whiteBackgroundPalette = Palette::fromFilename($imagePath, Color::fromHexToInt('#FFFFFF'));
        $this->assertSame([Color::fromHexToInt('#FF8080') => 1], iterator_to_array($whiteBackgroundPalette));

        $blackBackgroundPalette = Palette::fromFilename($imagePath, Color::fromHexToInt('#000000'));
        $this->assertSame([Color::fromHexToInt('#7E0000') => 1], iterator_to_array($blackBackgroundPalette));
    }
}
