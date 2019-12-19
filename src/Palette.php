<?php

namespace League\ColorExtractor;

use function arsort;
use function file_get_contents;
use function get_resource_type;
use function imagecolorat;
use function imagecolorsforindex;
use function imagecreatefromstring;
use function imagedestroy;
use function imageistruecolor;
use function imagesx;
use function imagesy;
use function is_resource;
use function sprintf;

final class Palette implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $colors;

    private function __construct()
    {
        $this->colors = [];
    }

    public static function fromFilename(string $filename, ?int $backgroundColor = null): Palette
    {
        $content = file_get_contents($filename);
        if (false === $content) {
            throw new \InvalidArgumentException('Can not extract content from the given file path.');
        }

        $image = imagecreatefromstring($content);
        if (false === $image) {
            throw new \InvalidArgumentException('Can not extract image from the given file path.');
        }

        $palette = self::fromGD($image, $backgroundColor);

        imagedestroy($image);

        return $palette;
    }

    /**
     * @param resource $image
     *
     * @throws \InvalidArgumentException
     */
    public static function fromGD($image, ?int $backgroundColor = null): Palette
    {
        if (!is_resource($image) || 'gd' != get_resource_type($image)) {
            throw new \InvalidArgumentException('Image must be a gd resource');
        }

        if (null !== $backgroundColor && ($backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $areColorsIndexed = !imageistruecolor($image);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $backgroundColorRed = 0;
        $backgroundColorGreen = 0;
        $backgroundColorBlue = 0;
        if (null !== $backgroundColor) {
            $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
            $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
            $backgroundColorBlue = $backgroundColor & 0xFF;
        }

        $palette = new self();
        for ($x = 0; $x < $imageWidth; ++$x) {
            for ($y = 0; $y < $imageHeight; ++$y) {
                $color = imagecolorat($image, $x, $y);
                if ($areColorsIndexed) {
                    $colorComponents = imagecolorsforindex($image, $color);
                    $color = ($colorComponents['alpha'] * 16777216) +
                        ($colorComponents['red'] * 65536) +
                        ($colorComponents['green'] * 256) +
                        ($colorComponents['blue']);
                }

                $alpha = $color >> 24;
                if (0 !== $alpha && null !== $backgroundColor) {
                    $alpha /= 127;
                    $color = (int) (($color >> 16 & 0xFF) * (1 - $alpha) + $backgroundColorRed * $alpha) * 65536 +
                        (int) (($color >> 8 & 0xFF) * (1 - $alpha) + $backgroundColorGreen * $alpha) * 256 +
                        (int) (($color & 0xFF) * (1 - $alpha) + $backgroundColorBlue * $alpha);
                }

                $palette->colors[$color] = $palette->colors[$color] ?? 0;
                ++$palette->colors[$color];
            }
        }

        arsort($palette->colors);

        return $palette;
    }

    public function count(): int
    {
        return count($this->colors);
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->colors);
    }

    public function getColorCount(int $color): int
    {
        return $this->colors[$color];
    }

    public function getMostUsedColors(?int $limit = null): array
    {
        return array_slice($this->colors, 0, $limit, true);
    }
}
