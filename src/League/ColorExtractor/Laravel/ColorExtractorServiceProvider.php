<?php

namespace League\ColorExtractor\Laravel;

use Illuminate\Support\ServiceProvider;
use League\ColorExtractor\Client;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ColorExtractorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('php-loep/color-extractor');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['color-extractor'] = $this->app->share(
            function ($app) {
                return new Client;
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('color-extractor');
    }
}
