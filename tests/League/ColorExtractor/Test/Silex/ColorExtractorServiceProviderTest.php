<?php

namespace League\ColorExtractor\Test;

use Silex\Application;
use League\ColorExtractor\Silex\ColorExtractorServiceProvider;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ColorExtractorServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    protected $jpegPath = './tests/assets/test.jpeg';
    protected $gifPath  = './tests/assets/test.gif';
    protected $pngPath  = './tests/assets/test.png';

    public function setUp()
    {
        $this->app = new Application();
        $this->app->register(new ColorExtractorServiceProvider());
    }

    public function testRegister()
    {
        $this->assertInstanceOf('League\\ColorExtractor\\Client', $this->app['color-extractor']);
    }

    public function testBoot()
    {
        $this->assertNull($this->app->boot());
    }

    public function testClient()
    {
        $image = $this->app['color-extractor']->loadJpeg($this->jpegPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);

        $image = $this->app['color-extractor']->loadGif($this->gifPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);

        $image = $this->app['color-extractor']->loadPng($this->pngPath);
        $this->assertInstanceOf('League\\ColorExtractor\\Image', $image);
    }
}
