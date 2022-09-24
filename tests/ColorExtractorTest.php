<?php

declare(strict_types=1);

namespace League\ColorExtractor\Tests;

use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use PHPUnit\Framework\TestCase;

final class ColorExtractorTest extends TestCase
{
    /**
     * @param string $imagePath
     * @param int    $colorCount
     * @param array<int, int> $expectedColors
     *
     * @dataProvider dataForTestExtract
     */
    public function testExtract(string $imagePath, int $colorCount, array $expectedColors): void
    {
        $palette = Palette::fromFilename($imagePath);
        $extractor = new ColorExtractor($palette);
        $colors = $extractor->extract($colorCount);

        self::assertSame($expectedColors, $colors);
    }

    public function dataForTestExtract(): iterable
    {
        yield [__DIR__ . '/assets/google.png', 0, []];
        yield [__DIR__ . '/assets/google.png', 1, [18417]];
        yield [__DIR__ . '/assets/google.png', 2, [18417, 42259]];
        yield [__DIR__ . '/assets/google.png', 3, [18417, 15080241, 42259]];
        yield [__DIR__ . '/assets/google.png', 4, [18417, 15080241, 42259, 16360960]];
        yield [__DIR__ . '/assets/google.png', 5, [18417, 15080241, 42259, 16360960, 4753405]];
        yield [__DIR__ . '/assets/empty.png', 0, []];
        yield [__DIR__ . '/assets/empty.png', 1, []];
    }
}
