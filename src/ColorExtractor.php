<?php

namespace League\ColorExtractor;

class ColorExtractor
{
    /**
     * @var Palette
     */
    protected $palette;

    /**
     * @var \SplFixedArray
     */
    protected $sortedColors;

    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }

    public function extract(int $colorCount = 1): array
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return self::mergeColors($this->sortedColors, $colorCount, 100 / $colorCount);
    }

    protected function isInitialized(): bool
    {
        return null !== $this->sortedColors;
    }

    protected function initialize(): void
    {
        $queue = new \SplPriorityQueue();
        $this->sortedColors = new \SplFixedArray(count($this->palette));

        $i = 0;
        foreach ($this->palette as $color => $count) {
            $labColor = self::intColorToLab($color);
            $queue->insert(
                $color,
                (sqrt($labColor['a'] * $labColor['a'] + $labColor['b'] * $labColor['b']) ?? 1) *
                (1 - $labColor['L'] / 200) *
                sqrt($count)
            );
            ++$i;
        }

        $i = 0;
        while ($queue->valid()) {
            $this->sortedColors[$i] = $queue->current();
            $queue->next();
            ++$i;
        }
    }

    protected static function mergeColors(\SplFixedArray $colors, int $limit, int $maxDelta): array
    {
        $limit = min(count($colors), $limit);
        if (1 === $limit) {
            return [$colors[0]];
        }
        $labCache = new \SplFixedArray($limit - 1);
        $mergedColors = [];

        foreach ($colors as $color) {
            $hasColorBeenMerged = false;

            $colorLab = self::intColorToLab($color);

            foreach ($mergedColors as $i => $mergedColor) {
                if (self::ciede2000DeltaE($colorLab, $labCache[$i]) < $maxDelta) {
                    $hasColorBeenMerged = true;
                    break;
                }
            }

            if ($hasColorBeenMerged) {
                continue;
            }

            $mergedColorCount = count($mergedColors);
            $mergedColors[] = $color;

            if ($mergedColorCount + 1 == $limit) {
                break;
            }

            $labCache[$mergedColorCount] = $colorLab;
        }

        return $mergedColors;
    }

    protected static function ciede2000DeltaE(array $firstLabColor, array $secondLabColor): float
    {
        $C1 = sqrt(pow($firstLabColor['a'], 2) + pow($firstLabColor['b'], 2));
        $C2 = sqrt(pow($secondLabColor['a'], 2) + pow($secondLabColor['b'], 2));
        $Cb = ($C1 + $C2) / 2;

        $G = .5 * (1 - sqrt(pow($Cb, 7) / (pow($Cb, 7) + pow(25, 7))));

        $a1p = (1 + $G) * $firstLabColor['a'];
        $a2p = (1 + $G) * $secondLabColor['a'];

        $C1p = sqrt(pow($a1p, 2) + pow($firstLabColor['b'], 2));
        $C2p = sqrt(pow($a2p, 2) + pow($secondLabColor['b'], 2));

        $h1p = 0 == $a1p && 0 == $firstLabColor['b'] ? 0 : atan2($firstLabColor['b'], $a1p);
        $h2p = 0 == $a2p && 0 == $secondLabColor['b'] ? 0 : atan2($secondLabColor['b'], $a2p);

        $LpDelta = $secondLabColor['L'] - $firstLabColor['L'];
        $CpDelta = $C2p - $C1p;

        if (0 == $C1p * $C2p) {
            $hpDelta = 0;
        } elseif (abs($h2p - $h1p) <= 180) {
            $hpDelta = $h2p - $h1p;
        } elseif ($h2p - $h1p > 180) {
            $hpDelta = $h2p - $h1p - 360;
        } else {
            $hpDelta = $h2p - $h1p + 360;
        }

        $HpDelta = 2 * sqrt($C1p * $C2p) * sin($hpDelta / 2);

        $Lbp = ($firstLabColor['L'] + $secondLabColor['L']) / 2;
        $Cbp = ($C1p + $C2p) / 2;

        if (0 == $C1p * $C2p) {
            $hbp = $h1p + $h2p;
        } elseif (abs($h1p - $h2p) <= 180) {
            $hbp = ($h1p + $h2p) / 2;
        } elseif ($h1p + $h2p < 360) {
            $hbp = ($h1p + $h2p + 360) / 2;
        } else {
            $hbp = ($h1p + $h2p - 360) / 2;
        }

        $T = 1 - .17 * cos($hbp - 30) + .24 * cos(2 * $hbp) + .32 * cos(3 * $hbp + 6) - .2 * cos(4 * $hbp - 63);

        $sigmaDelta = 30 * exp(-pow(($hbp - 275) / 25, 2));

        $Rc = 2 * sqrt(pow($Cbp, 7) / (pow($Cbp, 7) + pow(25, 7)));

        $Sl = 1 + ((.015 * pow($Lbp - 50, 2)) / sqrt(20 + pow($Lbp - 50, 2)));
        $Sc = 1 + .045 * $Cbp;
        $Sh = 1 + .015 * $Cbp * $T;

        $Rt = -sin(2 * $sigmaDelta) * $Rc;

        return sqrt(
            pow($LpDelta / $Sl, 2) +
            pow($CpDelta / $Sc, 2) +
            pow($HpDelta / $Sh, 2) +
            $Rt * ($CpDelta / $Sc) * ($HpDelta / $Sh)
        );
    }

    /**
     * @return array{L:float, a:float, b:float}
     */
    protected static function intColorToLab(int $color): array
    {
        return self::xyzToLab(
            self::srgbToXyz(
                self::rgbToSrgb(
                    [
                        'R' => ($color >> 16) & 0xFF,
                        'G' => ($color >> 8) & 0xFF,
                        'B' => $color & 0xFF,
                    ]
                )
            )
        );
    }

    protected static function rgbToSrgbStep(int $value): float
    {
        $value /= 255;

        return $value <= .03928 ?
            $value / 12.92 :
            pow(($value + .055) / 1.055, 2.4);
    }

    /**
     * @param array{R:int, G:int, B:int} $rgb
     *
     * @return array{R:float, G:float, B:float}
     */
    protected static function rgbToSrgb(array $rgb): array
    {
        return [
            'R' => self::rgbToSrgbStep($rgb['R']),
            'G' => self::rgbToSrgbStep($rgb['G']),
            'B' => self::rgbToSrgbStep($rgb['B']),
        ];
    }

    /**
     * @param array{R:float, G:float, B:float} $rgb
     *
     * @return array{X:float, Y:float, Z:float}
     */
    protected static function srgbToXyz($rgb)
    {
        return [
            'X' => (.4124564 * $rgb['R']) + (.3575761 * $rgb['G']) + (.1804375 * $rgb['B']),
            'Y' => (.2126729 * $rgb['R']) + (.7151522 * $rgb['G']) + (.0721750 * $rgb['B']),
            'Z' => (.0193339 * $rgb['R']) + (.1191920 * $rgb['G']) + (.9503041 * $rgb['B']),
        ];
    }

    protected static function xyzToLabStep(float $value): float
    {
        return $value > 216 / 24389 ? pow($value, 1 / 3) : 841 * $value / 108 + 4 / 29;
    }

    /**
     * @param array{X:float, Y:float, Z:float} $xyz
     *
     * @return array{L:float, a:float, b:float}
     */
    protected static function xyzToLab(array $xyz): array
    {
        //http://en.wikipedia.org/wiki/Illuminant_D65#Definition
        $Xn = .95047;
        $Yn = 1;
        $Zn = 1.08883;

        // http://en.wikipedia.org/wiki/Lab_color_space#CIELAB-CIEXYZ_conversions
        return [
            'L' => 116 * self::xyzToLabStep($xyz['Y'] / $Yn) - 16,
            'a' => 500 * (self::xyzToLabStep($xyz['X'] / $Xn) - self::xyzToLabStep($xyz['Y'] / $Yn)),
            'b' => 200 * (self::xyzToLabStep($xyz['Y'] / $Yn) - self::xyzToLabStep($xyz['Z'] / $Zn)),
        ];
    }
}
