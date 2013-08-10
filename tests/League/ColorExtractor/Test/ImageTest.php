<?php

namespace League\ColorExtractor\Test;

use League\ColorExtractor\Client;
use League\ColorExtractor\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    protected $jpegPath = './tests/assets/test.jpeg';
    protected $gifPath = './tests/assets/test.gif';
    protected $pngPath = './tests/assets/test.png';

    public function testJpegExtractSingleColor()
    {
        $this->client = new Client;

        $image = $this->client->loadJpeg($this->jpegPath);
        $palette = $image->extract();

        $this->assertInternalType('array', $palette);
        $this->assertCount(1, $palette);
        $this->assertEquals('#F3EC18', $palette[0]);
    }

    public function testGifExtractSingleColor()
    {
        $this->client = new Client;

        $image = $this->client->loadGif($this->gifPath);
        $palette = $image->extract();

        $this->assertInternalType('array', $palette);
        $this->assertCount(1, $palette);
        $this->assertEquals('#B772DB', $palette[0]);
    }

    public function testPngExtractSingleColor()
    {
        $this->client = new Client;

        $image = $this->client->loadPng($this->pngPath);
        $palette = $image->extract();

        $this->assertInternalType('array', $palette);
        $this->assertCount(1, $palette);
        $this->assertEquals('#FE6900', $palette[0]);
    }

    public function testJpegExtractMultipleColors()
    {
        $this->client = new Client;

        $image = $this->client->loadJpeg($this->jpegPath);

        $numColors = 3;

        $palette = $image->extract($numColors);

        $this->assertInternalType('array', $palette);
        $this->assertCount($numColors, $palette);
        $this->assertEquals($palette, array('#F3EC18', '#F49225', '#E82E31'));
    }

/*
    public function testJpegExtractMinColorRatioHigh()
    {
        $this->client = new Client;

        $image = $this->client->loadJpeg($this->jpegPath);

        $image->setMinColorRatio(1);

        $numColors = 3;
        $palette = $image->extract($numColors);

        $this->assertInternalType('array', $palette);
        $this->assertCount($numColors, $palette);
        $this->assertEquals($palette, array('#F3EC18', '#241B12', '#E1CF91'));
    }*/

}
