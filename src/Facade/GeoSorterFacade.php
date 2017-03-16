<?php
/**
 * Laralabs GeoSorter
 *
 * Postcode distance collection sorting package for Laravel 5+
 *
 * GeoSorter Facade
 *
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2017 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 */

namespace Laralabs\GeoSorter\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Html\HtmlBuilder
 */
class GeoSorterFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'GeoSorter'; }

}