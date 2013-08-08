<?php namespace League\ColorExtractor;

use Illuminate\Support\Facades\Facade;

class ColorExtractorFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'colorextractor'; }

}