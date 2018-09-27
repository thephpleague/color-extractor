<?php

declare(strict_types=1);

namespace League\ColorExtractor;

use Countable;
use IteratorAggregate;
use InvalidArgumentException;
use TypeError;

final class Palette implements Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $colors;

    private function __construct(array $colors)
    {
        arsort($colors);

        $this->colors = $colors;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->colors);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        foreach ($this->colors as $color => $count) {
            yield $color => $count;
        }
    }

    /**
     * @param int $color
     *
     * @return int
     */
    public function getColorCount(int $color): int
    {
        return $this->colors[$color] ?? 0;
    }

    /**
     * @param int $limit = null
     *
     * @return array
     */
    public function getMostUsedColors(int $limit = null): array
    {
        return array_slice($this->colors, 0, $limit, true);
    }

    /**
     * @param string   $filename
     * @param int|null $backgroundColor
     *
     * @return self
     */
    public static function fromFilename(string $filename, int $backgroundColor = null): self
    {
        $image = imagecreatefromstring(file_get_contents($filename));
        $palette = self::fromGD($image, $backgroundColor);
        imagedestroy($image);

        return $palette;
    }

    /**
     * @param resource $image
     * @param int|null $backgroundColor
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public static function fromGD($image, int $backgroundColor = null): self
    {
        if (!is_resource($image) || 'gd' != get_resource_type($image)) {
            throw new TypeError('Image must be a gd resource');
        }

        if (null !== $backgroundColor && ($backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $areColorsIndexed = !imageistruecolor($image);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
        $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
        $backgroundColorBlue = $backgroundColor & 0xFF;
        $colors = [];
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

                if ($alpha = $color >> 24) {
                    if (null === $backgroundColor) {
                        continue;
                    }

                    $alpha /= 127;
                    $color = (int) (($color >> 16 & 0xFF) * (1 - $alpha) + $backgroundColorRed * $alpha) * 65536 +
                             (int) (($color >> 8 & 0xFF) * (1 - $alpha) + $backgroundColorGreen * $alpha) * 256 +
                             (int) (($color & 0xFF) * (1 - $alpha) + $backgroundColorBlue * $alpha);
                }

                $colors[$color] = $colors[$color] ?? 0;
                ++$colors[$color];
            }
        }

        return new self($colors);
    }
}
