<?php

namespace League\ColorExtractor\Test;

use League\ColorExtractor\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $jpegPath = './tests/assets/test.jpeg';
    protected $gifPath = './tests/assets/test.gif';
    protected $pngPath = './tests/assets/test.png';

    public function testNewInstance()
    {
        $this->client = new Client;

        $image = $this->client->loadJpeg($this->jpegPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);

        $image = $this->client->loadGif($this->gifPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);

        $image = $this->client->loadPng($this->pngPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);
    }

}
