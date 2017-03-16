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

namespace Laralabs\GeoSorter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Ayeo\Geo\Coordinate;
use Ayeo\Geo\DistanceCalculator;

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
        $items          =   $collection;
        $distanceArray  =   [];
        $postcodeField  =   config('geosorter.postcodeField');
        $sourceOutcode  =   GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($postcode),0,-3)))->first();
        $source         =   new Coordinate\Decimal($sourceOutcode->lat, $sourceOutcode->long);

        foreach($items as $item)
        {
            $itemPostcode   =   $item->$postcodeField;
            $outcode        =   GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($itemPostcode),0,-3)))->first();

            //Calculate the distance
            $calculator     =   new DistanceCalculator();
            $destination    =   new Coordinate\Decimal($outcode->lat, $outcode->long);
            $distance       =   $calculator->getDistance($source, $destination);

            $distanceItem   =   [
                'id'        =>  $item->id,
                'distance'  =>  $distance
            ];
            $distanceArray[]    =   $distanceItem;
        }

        return $distanceArray;
    }
}
