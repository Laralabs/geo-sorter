<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter\Facade;

use Illuminate\Support\Facades\Facade;

class GeoSorter extends Facade
{
    public static function getFacadeRoot(): \Laralabs\GeoSorter\GeoSorter
    {
        return app('geosorter');
    }
}