<?php

namespace League\ColorExtractor\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use League\ColorExtractor\Client;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ColorExtractorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app['color-extractor'] = $app->share(function($app) {
            return new Client;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
