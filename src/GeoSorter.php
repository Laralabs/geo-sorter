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
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter;

use Illuminate\Database\Eloquent\Collection;
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

    /**
     * geoSort Function
     *
     * Sorts given collection based upon distance from the
     * given postcode, returns sorted collection.
     *
     * @param $collection
     * @param $postcode
     * @return mixed
     */
    public static function geoSort($collection, $postcode)
    {
        $items          =   $collection;
        $radius         =   0;
        /*
         * Pull in configuration options.
         */
        $postcodeField  =   config('geosorter.postcodeField');
        $sortOrder      =   config('geosorter.sortOrder');
        $distanceRadius =   config('geosorter.distanceRadius');
        $distanceUnit   =   config('geosorter.distanceUnit');

        /*
         * Calculate distance radius in metres
         */
        if($distanceRadius > 0){
            switch ($distanceUnit) {
                case 'metres':
                    //Metres is what we want, do nothing
                    $radius = $distanceRadius;
                    break;
                case 'kilometres':
                    $radius = $distanceRadius * 1000;
                    break;
                case 'miles':
                    $radius = $distanceRadius * 1609.34;
                    break;
                default:
                    $radius = 0;
                    break;
            }
        }
        /*
         * Make postcode uppercase and remove spaces
         */
        $postcode       =   str_replace(' ', '', strtoupper($postcode));
        /*
         * Check length of postcode to see if we are just dealing with
         * an outcode.
         */
        $length         =   strlen($postcode);
        if($length > 4) {
            $sourceOutcode  =   GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($postcode),0,-3)))->first();
        }else{
            $sourceOutcode  =   GeoSorterPostcodes::where('area_code', '=', $postcode)->first();
        }
        /*
         * Create Decimal object from source postcode latitude and longitude
         * coordinates.
         */
        $source         =   new Coordinate\Decimal($sourceOutcode->lat, $sourceOutcode->long);
        $mapData        =   [
            'postcodeField' =>  $postcodeField,
            'source'        =>  $source
        ];

        /*
         * Iterate through collection, work out item postcode distance from
         * source, map and populate 'distance' property on collection item.
         */
        $collection =   $items->map(function ($item) use ($mapData) {
            $postcodeField  =   $mapData['postcodeField'];
            $itemPostcode   =   $item->$postcodeField;
            /*
             * Make postcode uppercase and remove spaces
             */
            $itemPostcode   =   str_replace(' ', '', strtoupper($itemPostcode));
            /*
             * Check length of postcode to see if we are just dealing with
             * an outcode.
             */
            $length         =   strlen($itemPostcode);
            if ($length > 4) {
                $outcode    =   GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($itemPostcode), 0, -3)))->first();
            } else {
                $outcode    =   GeoSorterPostcodes::where('area_code', '=', $itemPostcode)->first();
            }

            if($outcode != null && $mapData['source'] != null) {
                /*
                 * Calculate the distance in metres
                 */
                $calculator         =   new DistanceCalculator();
                $destination        =   new Coordinate\Decimal($outcode->lat, $outcode->long);
                $distance           =   $calculator->getDistance($mapData['source'], $destination);
                $item['distance']   =   $distance;
            }

            return $item;
        });

        /*
         * Apply radius filter if it is set
         */
        if($radius > 0) {
            $collection =   $collection->filter(function($collect) use ($radius){
                return $collect->distance <= $radius;
            })->values();
        }

        /*
         * Sort the collection by 'distance' in the user defined order.
         */
        if($sortOrder == 'SORT_DESC') {
            $collection     =   $collection->sortByDesc('distance');
        }else{
            $collection     =   $collection->sortBy('distance');
        }

        return $collection;
    }
}
