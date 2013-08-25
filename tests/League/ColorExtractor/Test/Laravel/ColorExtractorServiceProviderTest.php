<?php

namespace League\ColorExtractor\Test\Laravel;

use League\ColorExtractor\Laravel\ColorExtractorServiceProvider;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ColorExtractorServiceProviderTest extends TestCase
{
    public function testIsProviderLoaded()
    {
        $loadedProviders = $this->app->getLoadedProviders();

        $this->assertArrayHasKey('League\\ColorExtractor\\Laravel\\ColorExtractorServiceProvider', $loadedProviders);
        $this->assertTrue($loadedProviders['League\\ColorExtractor\\Laravel\\ColorExtractorServiceProvider']);
    }

    public function testRegister()
    {
        $this->assertInstanceOf('League\\ColorExtractor\\Client', $this->app['color-extractor']);
    }
}
