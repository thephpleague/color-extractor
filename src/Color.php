<?php

declare(strict_types=1);

namespace League\ColorExtractor;

final class Color
{
    /**
     * @param int  $color
     * @param bool $prependHash = true
     *
     * @return string
     */
    public static function fromIntToHex(int $color, bool $prependHash = true): string
    {
        return ($prependHash ? '#' : '').sprintf('%06X', $color);
    }

    /**
     * @param string $color
     *
     * @return int
     */
    public static function fromHexToInt(string $color): int
    {
        return hexdec(ltrim($color, '#'));
    }

    /**
     * @param int $color
     *
     * @return array
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
     * @param array $components
     *
     * @return int
     */
    public static function fromRgbToInt(array $components): int
    {
        $components + ['r' => 0, 'g' => 0, 'b' => 0];

        return ($components['r'] * 65536) + ($components['g'] * 256) + ($components['b']);
    }
}
