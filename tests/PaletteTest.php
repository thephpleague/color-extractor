<?php

declare(strict_types=1);

namespace League\ColorExtractor\Tests;

use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use PHPUnit\Framework\TestCase;

class PaletteTest extends TestCase
{
    /**
     * @requires extension gd
     *
     * @dataProvider provideImages
     */
    public function testPaletteUsingGd(string $filename, array $expectedColors, ?int $backgroundColor = null): void
    {
        $this->assertSame($expectedColors, iterator_to_array(Palette::fromFilename($filename, $backgroundColor)));
    }

    /**
     * @requires extension imagick
     *
     * @dataProvider provideImages
     */
    public function testPaletteUsingImagick(string $filename, array $expectedColors, ?int $backgroundColor = null): void
    {
        $this->assertSame($expectedColors, iterator_to_array(Palette::fromImagick(new \Imagick($filename), $backgroundColor)));
    }

    public static function provideImages(): iterable
    {
        yield 'Many colors' => [
            __DIR__.'/assets/1x-black_3x-white.png',
            [
                Color::fromHexToInt('#FFFFFF') => 3,
                Color::fromHexToInt('#000000') => 1,
            ],
        ];

        yield 'Semi-transparent color without background' => [
            __DIR__.'/assets/1x-red-75.png',
            [],
        ];

        yield 'Semi-transparent color with background' => [
            __DIR__.'/assets/1x-red-75.png',
            [Color::fromHexToInt('#FF4040') => 1],
            Color::fromHexToInt('#FFFFFF'),
        ];

        yield 'Many colors blending into one' => [
            __DIR__.'/assets/1x-light-red_1x-red-75.png',
            [Color::fromHexToInt('#FF4040') => 2],
            Color::fromHexToInt('#FFFFFF'),
        ];

        yield 'Indexed color' => [
            __DIR__.'/assets/1x-indexed-white.png',
            [Color::fromHexToInt('#FFFFFF') => 1],
        ];
    }
}
