<?php

namespace League\ColorExtractor\Test;

use League\ColorExtractor\Color;

class ColorTest extends \PHPUnit_Framework_TestCase
{

    public function testItConvertsHexToRGB()
    {
        $rgb = Color::fromHexToRGB('#9B59BB');

        $this->assertEquals(['r' => 155, 'g' => 89, 'b' => 187], $rgb);

        $rgb = Color::fromHexToRGB('9B59BB');

        $this->assertEquals(['r' => 155, 'g' => 89, 'b' => 187], $rgb);

        $rgb = Color::fromHexToRGB('#00FFAA');

        $this->assertEquals(['r' => 0, 'g' => 255, 'b' => 170], $rgb);

        $rgb = Color::fromHexToRGB('00FFAA');

        $this->assertEquals(['r' => 0, 'g' => 255, 'b' => 170], $rgb);

        $rgb = Color::fromHexToRGB('#FFFFFF');

        $this->assertEquals(['r' => 255, 'g' => 255, 'b' => 255], $rgb);

        $rgb = Color::fromHexToRGB('FFFFFF');

        $this->assertEquals(['r' => 255, 'g' => 255, 'b' => 255], $rgb);

        $rgb = Color::fromHexToRGB('#000000');

        $this->assertEquals(['r' => 0, 'g' => 0, 'b' => 0], $rgb);

        $rgb = Color::fromHexToRGB('000000');

        $this->assertEquals(['r' => 0, 'g' => 0, 'b' => 0], $rgb);
    }

    public function testItConvertsIntToRGB()
    {
        $rgb = Color::fromIntToRGB(0);

        $this->assertEquals(['r' => 0, 'g' => 0, 'b' => 0], $rgb);

        $rgb = Color::fromIntToRGB('1448750');

        $this->assertEquals(['r' => 22, 'g' => 27, 'b' => 46], $rgb);

        $rgb = Color::fromIntToRGB('8555434');

        $this->assertEquals(['r' => 130, 'g' => 139, 'b' => 170], $rgb);

        $rgb = Color::fromIntToRGB('14281470');

        $this->assertEquals(['r' => 217, 'g' => 234, 'b' => 254], $rgb);
    }

    public function testItConvertsIntToHex()
    {
        $rgb = Color::fromIntToHex(0);

        $this->assertEquals('#000000', $rgb);

        $rgb = Color::fromIntToHex(1448750);

        $this->assertEquals('#161B2E', $rgb);

        $rgb = Color::fromIntToHex(8555434);

        $this->assertEquals('#828BAA', $rgb);

        $rgb = Color::fromIntToHex(14281470);

        $this->assertEquals('#D9EAFE', $rgb);
    }


    public function testItConvertsHexToInt()
    {
        $rgb = Color::fromHexToInt('#000000');

        $this->assertEquals(0, $rgb);

        $rgb = Color::fromHexToInt('#161B2E');

        $this->assertEquals(1448750, $rgb);

        $rgb = Color::fromHexToInt('#828BAA');

        $this->assertEquals(8555434, $rgb);

        $rgb = Color::fromHexToInt('#D9EAFE');

        $this->assertEquals(14281470, $rgb);
    }

}
