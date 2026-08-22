<?php

declare(strict_types=1);

namespace League\ColorExtractor;

/**
 * @phpstan-import-type IntColor from Color
 *
 * @phpstan-implements \IteratorAggregate<IntColor, positive-int>
 */
class Palette implements \Countable, \IteratorAggregate
{
    /** @var array<IntColor, positive-int> */
    protected $colors = [];

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
        $contents = @file_get_contents($filename);
        if (false === $contents) {
            throw new \InvalidArgumentException(sprintf('Failed to read "%s"', $filename));
        }

        return self::fromContents($contents, $backgroundColor);
    }

    /**
     * @param ?IntColor $backgroundColor
     */
    public static function fromUrl(string $url, ?int $backgroundColor = null): self
    {
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
        if (null !== $backgroundColor && (!is_numeric($backgroundColor) || $backgroundColor < 0 || $backgroundColor > 16777215)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $palette = new self();

        $areColorsIndexed = !imageistruecolor($image);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $palette->colors = [];

        $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
        $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
        $backgroundColorBlue = $backgroundColor & 0xFF;

        for ($x = 0; $x < $imageWidth; ++$x) {
            for ($y = 0; $y < $imageHeight; ++$y) {
                $color = imagecolorat($image, $x, $y);
                if (false === $color) {
                    throw new \RuntimeException(sprintf('Failed to get color at %d, %d', $x, $y));
                }
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

                /** @var IntColor $color */
                isset($palette->colors[$color]) ?
                    ++$palette->colors[$color] :
                    $palette->colors[$color] = 1;
            }
        }

        arsort($palette->colors);

        return $palette;
    }

    protected function __construct()
    {
        $this->colors = [];
    }
}
