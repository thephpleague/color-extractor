<?php

declare(strict_types=1);

namespace League\ColorExtractor;

use InvalidArgumentException;

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
        if ($color < 0 || $color > 16777215) {
            throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', $color));
        }

        return ($prependHash ? '#' : '').sprintf('%06X', $color);
    }

    /**
     * @param string $color
     *
     * @return int
     */
    public static function fromHexToInt(string $color): int
    {
        $value = $color;
        if ('#' === ($value[0] ?? '')) {
            $value = substr($value, 1);
        }

        if (3 === strlen($value)) {
            $value = $value[0].$value[0].$value[1].$value[1].$value[2].$value[2];
        }

        if (!preg_match('/^([A-F0-9]){6}$/i', $value)) {
            throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', $color));
        }

        return hexdec($value);
    }

    /**
     * @param int $color
     *
     * @return array
     */
    public static function fromIntToRgb(int $color): array
    {
        if ($color < 0 || $color > 16777215) {
            throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', $color));
        }

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
        foreach (['r', 'g', 'b'] as $offset) {
            if (!isset($components[$offset]) || $components[$offset] < 0 || $components[$offset] > 255) {
                throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', json_encode($components)));
            }
        }

        return ($components['r'] * 65536) + ($components['g'] * 256) + ($components['b']);
    }
}
