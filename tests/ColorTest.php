<?php

namespace LeagueTest\ColorExtractor;

use InvalidArgumentException;
use League\ColorExtractor\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    /**
     * @dataProvider hextToIntProvider
     */
    public function testFromHexToInt(string $hex, int $int)
    {
        self::assertSame($int, Color::fromHexToInt($hex));
    }

    /**
     * @dataProvider invalidHexProvider
     */
    public function testFromHexToIntThrowsInvalidArgumentException(string $hex)
    {
        self::expectException(InvalidArgumentException::class);
        Color::fromHexToInt($hex);
    }

    /**
     * @dataProvider intToHexProvider
     */
    public function testFromIntToHex(string $hex, int $int, bool $prependHash)
    {
        self::assertSame($hex, Color::fromIntToHex($int, $prependHash));
    }

    /**
     * @dataProvider invalidIntProvider
     */
    public function testFromIntToHexOverflow(int $value)
    {
        self::expectException(InvalidArgumentException::class);
        Color::fromIntToHex($value);
    }

    /**
     * @dataProvider rgbToIntProvider
     */
    public function testFromIntToRgb(array $rgb, int $int)
    {
        self::assertSame($int, Color::fromRgbToInt($rgb));
        self::assertSame($rgb, Color::fromIntToRgb($int));
    }

    /**
     * @dataProvider invalidIntProvider
     */
    public function testFromIntToRgbThrowsException(int $value)
    {
        self::expectException(InvalidArgumentException::class);
        Color::fromIntToRgb($value);
    }

    /**
     * @dataProvider invalidRgbProvider
     */
    public function testFromRgbToIntWithInvalidArray(array $rgb)
    {
        self::expectException(InvalidArgumentException::class);
        self::assertSame(0, Color::fromRgbToInt($rgb));
    }

    public function hextToIntProvider(): array
    {
        return [
            'black prefixed' => ['hex' => '#000000', 'int' => 0],
            'white prefixed' => ['hex' => '#FFFFFF', 'int' => 16777215],
            'black 3 letters' => ['hex' => '#000', 'int' => 0],
            'white 3 letters' => ['hex' => '#FFF', 'int' => 16777215],
            'random' => ['hex' => 'ABC123', 'int' => 11256099],
            'random case sensitive' => ['hex' => 'aBc123', 'int' => 11256099],
        ];
    }

    public function intToHexProvider(): array
    {
        return [
            'black' => ['hex' => '#000000', 'int' => 0, 'prependHash' => true],
            'white' => ['hex' => '#FFFFFF', 'int' => 16777215, 'prependHash' => true],
            'random' => ['hex' => '#ABC123', 'int' => 11256099, 'prependHash' => true],
            'black unprefixed' => ['hex' => '000000', 'int' => 0, 'prependHash' => false],
            'white unprefixed' => ['hex' => 'FFFFFF', 'int' => 16777215, 'prependHash' => false],
            'random unprefixed' => ['hex' => 'ABC123', 'int' => 11256099, 'prependHash' => false],
        ];
    }

    public function rgbToIntProvider(): array
    {
        return [
            'black' => ['rgb' => ['r' => 0, 'g' => 0, 'b' => 0], 'int' => 0],
            'white' => ['rgb' => ['r' => 255, 'g' => 255, 'b' => 255], 'int' => 16777215],
            'random' => ['rgb' => ['r' => 171, 'g' => 193, 'b' => 35], 'int' => 11256099],
        ];
    }

    public function invalidRgbProvider(): array
    {
        return [
            'empty array' => [[]],
            'over' => [['r' => 256, 'g' => 0, 'b' => 0]],
            'under' => [['r' => 0, 'g' => -1, 'b' => 0]],
        ];
    }

    public function invalidIntProvider()
    {
        return [
            'over' => [16777216],
            'under' => [-1],
        ];
    }

    public function invalidHexProvider()
    {
        return [
            'invalid hex representation' => ['hex' => '#FoOBaR'],
            'invalid hex representation without #' => ['hex' => 'FOObAR'],
            'invalid 3 char hex representation' => ['hex' => '#FOO'],
            'invalid 3 char hex representation without #' => ['hex' => 'FoO'],
            'invalid not 3 or 6 char representation' => ['hex' => 'AB'],
        ];
    }
}
