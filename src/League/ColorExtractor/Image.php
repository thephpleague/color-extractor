<?php

namespace League\ColorExtractor;

class Image
{
    /** 
     * @var resource Image resource identifier, as returned by imagecreatefromjpeg()
     */
    protected $imageResource;

    /** 
     * @var int Minimum ratio, below colors are ignored (0 - 1)
     */
    protected $minColorRatio = 0;

    /** 
     * @var int Minimum saturation level, below colors are ignored (0 - 1)
     */
    protected $minSaturation = 0;

    public function __construct($imageResource)
    {
        $this->imageResource = $imageResource;
    }

    public function setMinColorRatio($minColorRatio)
    {
        $this->minColorRatio = $minColorRatio;
        return $this;
    }

    public function getMinColorRatio($minColorRatio)
    {
        return $this->minColorRatio;
    }

    public function setMinSaturation($minSaturation)
    {
        $this->minSaturation = $minSaturation;
        return $this;
    }

    public function getMinSaturation($minSaturation)
    {
        return $this->minSaturation;
    }

    public function extract($maxPaletteSize = 1)
    {
        $w = imagesx($this->imageResource);
        $h = imagesy($this->imageResource);

        $x = $y = 0;

        $colors = array();

        do {
            do {
                $rgba = imagecolorsforindex($this->imageResource, imagecolorat($this->imageResource, $x, $y));
                $rgb = array($rgba['red'], $rgba['green'], $rgba['blue']);
                $color = hexdec(sprintf('%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]));
                
                if (array_key_exists($color, $colors)) {
                    $colors[$color]['count']++;
                } else {
                    $saturation = self::getColorSaturation(self::getSRGBComponents($rgb));
                    if ($saturation >= $this->minSaturation) {
                        $colors[$color] = array(
                            'count' => 1,
                            'saturation' => $saturation
                        );
                    }
                }
            } while (++$y < $h);
            $y = 0;
        } while (++$x < $w);

        uasort(
            $colors, 
            function ($firstColor, $secondColor) {
                $diff = $firstColor['saturation']*$firstColor['count'] - $secondColor['saturation']*$secondColor['count'];
                return !$diff ?
                    ($firstColor['saturation'] > $secondColor['saturation'] ? 1 : -1) :
                    ($diff < 0 ? 1 : -1);
            }
        );

        $totalColorCount = count($colors);
        $maxPaletteSize = min($maxPaletteSize, $totalColorCount);
        $minDeltaE = 100/($maxPaletteSize + 1);
        $minCountAllowed = $totalColorCount * $this->minColorRatio;
        $paletteSize = 1;

        if ($maxPaletteSize > 1) {
            $i = 0;
            $mergeCount = 0;
            while ($i++ < $maxPaletteSize) {
                $j = 0;
                reset($colors);
                while (++$j < $i) {
                    next($colors);
                }
                $refColor = key($colors);
                $refColorData = current($colors);

                if ($refColorData['count'] <= $minCountAllowed) {
                    break;
                }

                if (array_key_exists('Lab', $refColorData)) {
                    $refLab = $refColorData['Lab'];
                } else {
                    $refLab = self::getLabFromColor($refColor);
                    $colors[$refColor]['Lab'] = $refLab;
                }

                if ($mergeCount) {
                    $offset = max($i, $maxPaletteSize - $mergeCount - 1);
                    while ($j++ < $offset) {
                        next($colors);
                    }
                    $mergeCount = 0;
                }

                while ($j++ < $maxPaletteSize) {
                    $cmpColorData = next($colors);
                    $cmpColor = key($colors);
                    if ($colors[$cmpColor]['count'] <= $minCountAllowed) {
                        break;
                    }

                    if (array_key_exists('Lab', $cmpColorData)) {
                        $cmpLab = $cmpColorData['Lab'];
                    } else {
                        $cmpLab = self::getLabFromColor($cmpColor);
                        $colors[$cmpColor]['Lab'] = $cmpLab;
                    }

                    if (self::Ciede2000DeltaE($refLab, $cmpLab) <= $minDeltaE) {
                        $j--;
                        $mergeCount++;
                        prev($colors);
                        unset($colors[$cmpColor]);
                        if ($i > 1) {
                            $i = 0;
                        }
                    }
                }
            }
            $paletteSize = max(1, $i - 1);
        }
        return array_map(
            array(__CLASS__, 'toHex'),
            array_keys(array_slice($colors, 0, $paletteSize, true))
        );
    }

    protected function toHex($color)
    {
        $rgb = self::getRGBComponents($color);
        return sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    }

    protected function getLabFromColor($color)
    {
        return self::getLabFromSRGB(self::getSRGBComponents(self::getRGBComponents($color)));
    }

    protected function getLabFromSRGB($sRGBComponents)
    {
        return self::getLabComponents(self::getXYZComponents($sRGBComponents));
    }

    protected function getRGBComponents($color)
    {
        return array(
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF
        );
    }

    protected function getColorSaturation(array $sRGBComponents)
    {
        $max = max($sRGBComponents);
        $min = min($sRGBComponents);
        $diff = $max - $min;
        $sum = $max + $min;

        // No division by zero please
        if ($diff == 0 or $sum == 0) {
            return 0;
        } elseif ($sum / 2 > .5) {
            return $diff / (2 - $diff);
        } else { 
            return $diff / $sum;
        }
    }

    protected function getSRGBComponents($RGBComponents)
    {
        return array(
            self::getSRGBComponent($RGBComponents[0]),
            self::getSRGBComponent($RGBComponents[1]),
            self::getSRGBComponent($RGBComponents[2])
        );
    }

    protected function getSRGBComponent($component)
    {
        $component/=255;
        return $component <= .03928 ?
            $component/12.92 :
            pow(($component + .055)/1.055, 2.4);
    }

    protected function getXYZComponents($sRGBComponents)
    {
        list($r, $g, $b) = $sRGBComponents;
        return array(
            .4124*$r + .3576*$g + .1805*$b,
            .2126*$r + .7152*$g + .0722*$b,
            .0193*$r + .1192*$g + .9505*$b
        );
    }

    protected function getLabComponents($XYZComponents)
    {
        list($x, $y, $z) = $XYZComponents;
        $fY = $this->XyzToLabStep($y);
        return array(
            116*$fY - 16,
            500*($this->XyzToLabStep($x) - $fY),
            200*($fY - $this->XyzToLabStep($z))
        );
    }

    protected function XyzToLabStep($XYZComponent)
    {
        return $XYZComponent > pow(6/29, 3) ?
            pow($XYZComponent, 1/3) :
            (1/3)*pow(29/6, 2)*$XYZComponent + (4/29);
    }

    protected function Ciede2000DeltaE($firstLabColor, $secondLabColor)
    {
        list($L1, $a1, $b1) = $firstLabColor;
        list($L2, $a2, $b2) = $secondLabColor;

        $C1 = sqrt(pow($a1, 2) + pow($b1, 2));
        $C2 = sqrt(pow($a2, 2) + pow($b2, 2));
        $Cb = ($C1 + $C2)/2;

        $G = .5*(1 - sqrt(pow($Cb, 7)/(pow($Cb, 7) + pow(25, 7))));

        $a1p = (1 + $G)*$a1;
        $a2p = (1 + $G)*$a2;

        $C1p = sqrt(pow($a1p, 2) + pow($b1, 2));
        $C2p = sqrt(pow($a2p, 2) + pow($b2, 2));

        $h1p = $a1p == 0 && $b1 == 0 ? 0 : atan2($b1, $a1p);
        $h2p = $a2p == 0 && $b2 == 0 ? 0 : atan2($b2, $a2p);

        $LpDelta = $L2 - $L1;
        $CpDelta = $C2p - $C1p;

        if ($C1p*$C2p == 0) {
            $hpDelta = 0;
        } elseif (abs($h2p - $h1p) <= 180) {
            $hpDelta = $h2p - $h1p;
        } elseif ($h2p - $h1p > 180) {
            $hpDelta = $h2p - $h1p - 360;
        } else {
            $hpDelta = $h2p - $h1p + 360;
        }

        $HpDelta = 2*sqrt($C1p*$C2p)*sin($hpDelta/2);

        $Lbp = ($L1 + $L2)/2;
        $Cbp = ($C1p + $C2p)/2;

        if ($C1p*$C2p == 0) {
            $hbp = $h1p + $h2p;
        } elseif (abs($h1p - $h2p) <= 180) {
            $hbp = ($h1p + $h2p)/2;
        } elseif ($h1p + $h2p < 360) {
            $hbp = ($h1p + $h2p + 360)/2;
        } else {
            $hbp = ($h1p + $h2p - 360)/2;
        }

        $T = 1 - .17*cos($hbp - 30) + .24*cos(2*$hbp) + .32*cos(3*$hbp + 6) - .2*cos(4*$hbp - 63);

        $sigmaDelta = 30*exp(-pow(($hbp - 275)/25, 2));

        $Rc = 2*sqrt(pow($Cbp, 7)/(pow($Cbp, 7) + pow(25, 7)));

        $Sl = 1 + ((.015*pow($Lbp - 50, 2))/sqrt(20 + pow($Lbp - 50, 2)));
        $Sc = 1 + .045*$Cbp;
        $Sh = 1 + .015*$Cbp*$T;

        $Rt = -sin(2*$sigmaDelta)*$Rc;

        return sqrt(
            pow($LpDelta/$Sl, 2) +
            pow($CpDelta/$Sc, 2) +
            pow($HpDelta/$Sh, 2) +
            $Rt*($CpDelta/$Sc)*($HpDelta/$Sh)
        );
    }
}
