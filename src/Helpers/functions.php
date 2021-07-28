<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

use Laralabs\GeoSorter\GeoSorter;

if (!function_exists('geo_sorter')) {
    function geo_sorter(): GeoSorter
    {
        return app('geosorter');
    }
}
