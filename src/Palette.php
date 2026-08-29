<?php

namespace League\ColorExtractor;

use League\ColorExtractor\PixelIterator\StandalonePngIterator;

/**
 * @phpstan-import-type IntColor from Color
 *
 * @phpstan-implements \IteratorAggregate<IntColor, positive-int>
 */
class Palette implements \Countable, \IteratorAggregate
{
    /** @var array<IntColor, positive-int> */
    protected array $colors = [];

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return \count($this->colors);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->colors;
    }

    /**
     * @param IntColor $color
     *
     * @return int<0, max>
     */
    public function getColorCount(int $color): int
    {
        return $this->colors[$color] ?? 0;
    }

    /**
     * @param ?int<0, max> $limit
     *
     * @return array<IntColor, positive-int>
     */
    public function getMostUsedColors(?int $limit = null): array
    {
        return \array_slice($this->colors, 0, $limit, true);
    }

    /**
     * @param ?IntColor $backgroundColor
     */
    public static function fromFilename(string $filename, ?int $backgroundColor = null): self
    {
        if (!\extension_loaded('gd')) {
            throw new \LogicException(\sprintf('"%s()" requires the "gd" extension, enable it or call "fromImagick()" instead.', __METHOD__));
        }

        $contents = @file_get_contents($filename);
        if (false === $contents) {
            throw new \InvalidArgumentException(\sprintf('Failed to read "%s"', $filename));
        }

        return self::fromContents($contents, $backgroundColor);
    }

    /**
     * @param non-empty-string $url
     * @param ?IntColor        $backgroundColor
     */
    public static function fromUrl(string $url, ?int $backgroundColor = null): self
    {
        if (!\extension_loaded('gd')) {
            throw new \LogicException(\sprintf('"%s()" requires the "gd" extension, enable it or call "fromImagick()" instead.', __METHOD__));
        }

        if (!\function_exists('curl_init')) {
            return self::fromFilename($url, $backgroundColor);
        }

        $ch = curl_init();
        try {
            curl_setopt($ch, \CURLOPT_URL, $url);
            curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);

            /** @var string|false $contents */
            $contents = curl_exec($ch);
            if (false === $contents) {
                throw new \RuntimeException('Failed to fetch image from URL');
            }
        } finally {
            curl_close($ch);
        }

        return self::fromContents($contents, $backgroundColor);
    }

    /**
     * @param ?IntColor $backgroundColor
     */
    public static function fromContents(string $contents, ?int $backgroundColor = null): self
    {
        if (!\extension_loaded('gd')) {
            throw new \LogicException(\sprintf('"%s()" requires the "gd" extension, enable it or call "fromImagick()" instead.', __METHOD__));
        }

        $image = imagecreatefromstring($contents);
        if (false === $image) {
            throw new \RuntimeException('Failed to load image');
        }

        $palette = self::fromGD($image, $backgroundColor);

        if (version_compare(\PHP_VERSION, '8.0.0', '<')) {
            imagedestroy($image);
        }

        return $palette;
    }

    /**
     * @param \GdImage|resource $image
     * @param ?IntColor         $backgroundColor
     */
    public static function fromGD($image, ?int $backgroundColor = null): self
    {
        if (!$image instanceof \GdImage && (!\is_resource($image) || 'gd' !== get_resource_type($image))) {
            throw new \InvalidArgumentException('Image must be a gd resource');
        }
        if (null !== $backgroundColor && ($backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new \InvalidArgumentException(\sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $palette = new self();

        $areColorsIndexed = !imageistruecolor($image);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
        $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
        $backgroundColorBlue = $backgroundColor & 0xFF;

        for ($x = 0; $x < $imageWidth; ++$x) {
            for ($y = 0; $y < $imageHeight; ++$y) {
                $color = imagecolorat($image, $x, $y);
                if (false === $color) {
                    throw new \RuntimeException(\sprintf('Failed to get color at %d, %d', $x, $y));
                }
                if ($areColorsIndexed) {
                    $colorComponents = imagecolorsforindex($image, $color);
                    $color = ($colorComponents['alpha'] * 16777216) +
                             ($colorComponents['red'] * 65536) +
                             ($colorComponents['green'] * 256) +
                             $colorComponents['blue'];
                }

                if ($transparency = $color >> 24) {
                    if (null === $backgroundColor) {
                        continue;
                    }

                    $transparency /= 127;
                    $color = (int) (($color >> 16 & 0xFF) * (1 - $transparency) + $backgroundColorRed * $transparency) * 65536 +
                             (int) (($color >> 8 & 0xFF) * (1 - $transparency) + $backgroundColorGreen * $transparency) * 256 +
                             (int) (($color & 0xFF) * (1 - $transparency) + $backgroundColorBlue * $transparency);
                }

                /** @var IntColor $color */
                isset($palette->colors[$color])
                    ? ++$palette->colors[$color]
                    : $palette->colors[$color] = 1
                ;
            }
        }

        arsort($palette->colors);

        return $palette;
    }

    public static function fromImagick(\Imagick $image, ?int $backgroundColor = null): self
    {
        if (null !== $backgroundColor && ($backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new \InvalidArgumentException(\sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $palette = new self();

        if (1 !== $image->count()) {
            $image->rewind();
            $image = $image->current();
        }

        $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
        $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
        $backgroundColorBlue = $backgroundColor & 0xFF;

        /** @var \ImagickPixel $pixel */
        foreach ($image->getImageHistogram() as $pixel) {
            $components = $pixel->getColor(2);
            $color = Color::fromRgbToInt($components);

            $opacity = $components['a'];
            if (255 !== $opacity) {
                if (null === $backgroundColor) {
                    continue;
                }

                $opacity /= 255;
                $color = (int) (($color >> 16 & 0xFF) * $opacity + $backgroundColorRed * (1 - $opacity)) * 65536 +
                    (int) (($color >> 8 & 0xFF) * $opacity + $backgroundColorGreen * (1 - $opacity)) * 256 +
                    (int) (($color & 0xFF) * $opacity + $backgroundColorBlue * (1 - $opacity));
            }

            isset($palette->colors[$color])
                ? ++$palette->colors[$color]
                : $palette->colors[$color] = 1
            ;
        }

        arsort($palette->colors);

        return $palette;
    }

    public static function fromImagickPixelIterator(\Imagick $image, ?int $backgroundColor = null): self
    {
        if (null !== $backgroundColor && ($backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new \InvalidArgumentException(\sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $palette = new self();

        if (1 !== $image->count()) {
            $image->rewind();
            $image = $image->current();
        }

        $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
        $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
        $backgroundColorBlue = $backgroundColor & 0xFF;

        /** @var \ImagickPixel $pixel */
        foreach ($image->getPixelIterator() as $row) {
            foreach ($row as $pixel) {
                $components = $pixel->getColor(2);
                $color = Color::fromRgbToInt($components);

                $opacity = $components['a'];
                if (255 !== $opacity) {
                    if (null === $backgroundColor) {
                        continue;
                    }

                    $opacity /= 255;
                    $color = (int) (($color >> 16 & 0xFF) * $opacity + $backgroundColorRed * (1 - $opacity)) * 65536 +
                        (int) (($color >> 8 & 0xFF) * $opacity + $backgroundColorGreen * (1 - $opacity)) * 256 +
                        (int) (($color & 0xFF) * $opacity + $backgroundColorBlue * (1 - $opacity));
                }

                /** @var IntColor $color */
                /** @var positive-int $colorCount */
                $colorCount = $pixel->getColorCount();

                isset($palette->colors[$color])
                    ? $palette->colors[$color] += $colorCount
                    : $palette->colors[$color] = $colorCount;
            }
        }

        arsort($palette->colors);

        return $palette;
    }

    public static function fromIterator(StandalonePngIterator $iterator, ?int $backgroundColor = null): self
    {
        if (null !== $backgroundColor && ($backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new \InvalidArgumentException(\sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $palette = new self();

        foreach ($iterator as $color) {
            isset($palette->colors[$color])
                ? ++$palette->colors[$color]
                : $palette->colors[$color] = 1
            ;
        }

        arsort($palette->colors);

        return $palette;
    }

    protected function __construct()
    {
        $this->colors = [];
    }
}
