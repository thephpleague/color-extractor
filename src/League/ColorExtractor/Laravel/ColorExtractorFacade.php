<?php

namespace League\ColorExtractor\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ColorExtractorFacade extends Facade
{
    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor()
    {
        return 'color-extractor';
    }
}
