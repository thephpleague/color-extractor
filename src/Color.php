<?php

declare(strict_types=1);

namespace League\ColorExtractor;

/**
 * @phpstan-type IntColor int<0, 16777215>
 * @phpstan-type RgbColor array{r: int<0, 255>, g: int<0, 255>, b: int<0, 255>}
 */
class Color
{
    /**
     * @param IntColor $color
     */
    public static function fromIntToHex(int $color, bool $prependHash = true): string
    {
        return ($prependHash ? '#' : '').sprintf('%06X', $color);
    }

    /**
     * @return IntColor
     */
    public static function fromHexToInt(string $color): int
    {
        /** @var int<0, max> $intColor */
        $intColor = hexdec(ltrim($color, '#'));
        if ($intColor > 16777215) {
            throw new \DomainException('Only 24-bit colors are supported');
        }

        return $intColor;
    }

    /**
     * @return RgbColor
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
     * @param RgbColor $components
     *
     * @return IntColor
     */
    public static function fromRgbToInt(array $components): int
    {
        return ($components['r'] * 65536) + ($components['g'] * 256) + ($components['b']);
    }
}
