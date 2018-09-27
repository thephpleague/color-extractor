<?php

declare(strict_types=1);

namespace League\ColorExtractor;

use InvalidArgumentException;

final class Color
{
    const BLACK = 0;
    const WHITE = 16777215;

    /**
     * @param int  $color
     * @param bool $prependHash
     *
     * @return string
     */
    public static function fromIntToHex(int $color, bool $prependHash = true): string
    {
        return ($prependHash ? '#' : '').sprintf('%06X', self::filterIntColor($color));
    }

    /**
     * Validate the color value.
     *
     * @param int $color
     *
     * @throws InvalidArgumentException if the value does not represent a valid color
     *
     * @return int
     */
    private static function filterIntColor(int $color): int
    {
        if ($color >= self::BLACK && $color <= self::WHITE) {
            return $color;
        }

        throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', $color));
    }

    /**
     * @param string $color
     *
     * @throws InvalidArgumentException if the value does not represent a valid color
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
        $color = self::filterIntColor($color);

        return [
            'r' => $color >> 16 & 0xFF,
            'g' => $color >> 8 & 0xFF,
            'b' => $color & 0xFF,
        ];
    }

    /**
     * @param array $rgb
     *
     * @throws InvalidArgumentException if the value does not represent a valid color
     *
     * @return int
     */
    public static function fromRgbToInt(array $rgb): int
    {
        foreach (['r', 'g', 'b'] as $offset) {
            if (!isset($rgb[$offset]) || $rgb[$offset] < 0 || $rgb[$offset] > 255) {
                throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', json_encode($rgb)));
            }
        }

        return ($rgb['r'] * 65536) + ($rgb['g'] * 256) + $rgb['b'];
    }
}
