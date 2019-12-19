<?php

namespace League\ColorExtractor;

final class Color
{
    public static function fromIntToHex(int $color, bool $prependHash = true): string
    {
        return ($prependHash ? '#' : '').sprintf('%06X', $color);
    }

    public static function fromHexToInt(string $color): int
    {
        return (int) hexdec(ltrim($color, '#'));
    }

    /**
     * @return array{r:int, g:int, b:int}
     */
    public static function fromIntToRgb(int $color): array
    {
        return [
            'r' => $color >> 16 & 0xFF,
            'g' => $color >> 8 & 0xFF,
            'b' => $color & 0xFF,
        ];
    }

    /**
     * @param array{r:int, g:int, b:int} $components
     */
    public static function fromRgbToInt(array $components): int
    {
        return ($components['r'] * 65536) + ($components['g'] * 256) + ($components['b']);
    }
}
