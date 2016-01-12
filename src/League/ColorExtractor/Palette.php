<?php

namespace League\ColorExtractor;

class Palette implements \Countable, \IteratorAggregate
{
    /** @var array */
    protected $colors;

    /**
     * @return int
     */
    public function count()
    {
        return count($this->colors);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->colors);
    }

    /**
     * @param int $color
     *
     * @return int
     */
    public function getColorCount($color)
    {
        return $this->colors[$color];
    }

    /**
     * @param int $limit = null
     *
     * @return array
     */
    public function getMostUsedColors($limit = null)
    {
        return array_slice($this->colors, 0, $limit, true);
    }

    /**
     * @param string $filename
     *
     * @return Palette
     */
    public static function fromFilename($filename)
    {
        return self::fromGD(imagecreatefromstring(file_get_contents($filename)));
    }

    /**
     * @param resource $image
     *
     * @return Palette
     *
     * @throws \InvalidArgumentException
     */
    public static function fromGD($image)
    {
        if (!is_resource($image) || get_resource_type($image) != 'gd') {
            throw new \InvalidArgumentException('Image must be a gd resource');
        }

        $palette = new self();

        $areColorsIndexed = !imageistruecolor($image);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $palette->colors = [];

        for ($x = 0; $x < $imageWidth; ++$x) {
            for ($y = 0; $y < $imageHeight; ++$y) {
                $color = imagecolorat($image, $x, $y);
                if ($areColorsIndexed) {
                    $colorComponents = imagecolorsforindex($image, $color);
                    $color = ($colorComponents['red'] * 65536) + ($colorComponents['green'] * 256) + ($colorComponents['blue']);
                }

                isset($palette->colors[$color]) ?
                    $palette->colors[$color] += 1 :
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
