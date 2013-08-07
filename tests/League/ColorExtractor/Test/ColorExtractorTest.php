<?php

namespace League\ColorExtractor\Test;

use League\ColorExtractor\Client as ColorExtractor;

class ColorExtractorTest extends \PHPUnit_Framework_TestCase
{
    protected $spongebobPath = './tests/assets/1.jpeg';
    protected $spongebobResource;

    protected $cashcatPath = './tests/assets/2.jpeg';
    protected $cashcatResource;

    public function setUp()
    {
        $this->spongebobResource = imagecreatefromjpeg($this->spongebobPath);
        $this->cashcatResource = imagecreatefromjpeg($this->cashcatPath);
    }

    public function testExtractSingleColorSpongebobJpeg()
    {
        $palette = ColorExtractor::extract($this->spongebobResource);
        $this->assertEquals($palette[0], '#F3EC18');
    }

    public function testExtractSingleColorCashcatJpeg()
    {
        $palette = ColorExtractor::extract($this->cashcatResource);
        $this->assertEquals($palette[0], '#010000');
    }

    public function testExtractMultipleColorsSpongebobJpeg()
    {
        $palette = ColorExtractor::extract($this->spongebobResource, 3);
        $this->assertEquals($palette, array('#F3EC18', '#241B12', '#E1CF91'));
    }

    public function testExtractMultipleColorsCashcatJpeg()
    {
        $palette = ColorExtractor::extract($this->cashcatResource, 3);
        $this->assertEquals($palette, array('#010000', '#66401B', '#5B3B15'));
    }

}
