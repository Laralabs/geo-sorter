<?php
/**
 * Laralabs GeoSorter
 *
 * Postcode distance collection sorting package for Laravel 5+
 *
 * GeoSorter Class
 *
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2017 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 */

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GeoSorter extends Collection
{
    /**
     *
     * @param string $collection
     * @param string $postcode
     */
    public function __construct($collection, $postcode)
    {
        parent::__construct();
    }

    public static function geoSort($collection, $postcode)
    {
        $items = $collection;
        $distanceArray = [];

        foreach($items as $item)
        {

        }
    }
}
