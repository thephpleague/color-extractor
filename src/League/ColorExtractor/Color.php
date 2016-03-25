<?php

namespace League\ColorExtractor;

class Color
{
    /**
     * @param int $color
     * @param bool $prependHash = true
     *
     * @return string
     */
    public static function fromIntToHex($color, $prependHash = true)
    {
        return ($prependHash ? '#' : '') . sprintf('%02X%02X%02X', ($color >> 16) & 0xFF, ($color >> 8) & 0xFF,
            $color & 0xFF);
    }

    /**
     * @param string $color
     *
     * @return int
     */
    public static function fromHexToInt($color)
    {
        return hexdec(ltrim($color, '#'));
    }

    /**
     * @param string $color
     *
     * @return array
     */
    public static function fromHexToRGB($color)
    {
        list($r, $g, $b) = sscanf(ltrim($color, '#'), "%2x%2x%2x");

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    /**
     * @param int $color
     *
     * @return array
     */
    public static function fromIntToRGB($color)
    {
        $color = self::fromIntToHex($color);

        return self::fromHexToRGB($color);
    }
}
