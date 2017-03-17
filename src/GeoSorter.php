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
        $sortOrder      =   config('geosorter.sortOrder');
        //Tidy up the postcode, make uppercase and remove spaces
        $postcode       =   str_replace(' ', '', $postcode);
        $postcode       =   strtoupper($postcode);
        $length         =   strlen($postcode);
        if($length > 4) {
            $sourceOutcode  =   GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($postcode),0,-3)))->first();
        }else{
            $sourceOutcode  =   GeoSorterPostcodes::where('area_code', '=', $postcode)->first();
        }
        $source         =   new Coordinate\Decimal($sourceOutcode->lat, $sourceOutcode->long);
        $mapData        =   [
            'postcodeField' =>  $postcodeField,
            'source'        =>  $source
        ];
        $collection = $items->map(function ($item) use ($mapData) {
            $postcodeField = $mapData['postcodeField'];
            $itemPostcode = $item->$postcodeField;
            //Tidy up the postcode, make uppercase and remove spaces
            $itemPostcode = str_replace(' ', '', $itemPostcode);
            $itemPostcode = strtoupper($itemPostcode);
            $length = strlen($itemPostcode);
            if ($length > 4) {
                $trimmed = trim(substr(trim($itemPostcode), 0, -3));
                $outcode = GeoSorterPostcodes::where('area_code', '=', $trimmed)->first();
            } else {
                $outcode = GeoSorterPostcodes::where('area_code', '=', $itemPostcode)->first();
            }

            //Calculate the distance
            $calculator = new DistanceCalculator();
            $destination = new Coordinate\Decimal($outcode->lat, $outcode->long);
            $distance = $calculator->getDistance($mapData['source'], $destination);

            $item['distance'] = $distance;
            return $item;
        });

        /*
         * Sort the results by 'distance' in the user defined order.
         */
        if($sortOrder == 'SORT_DESC') {
            $collection = $collection->sortByDesc('distance');
        }else{
            $collection = $collection->sortBy('distance');
        }

        return $collection;
    }
}
