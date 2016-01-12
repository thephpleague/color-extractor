<?php

namespace League\ColorExtractor;

class Color
{
    /**
     * @param int  $color
     * @param bool $prependHash = true
     *
     * @return string
     */
    public static function fromIntToHex($color, $prependHash = true)
    {
        return ($prependHash ? '#' : '').sprintf('%02X%02X%02X', ($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF);
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
}
