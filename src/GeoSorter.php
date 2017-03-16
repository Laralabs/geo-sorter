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

        foreach($items as $item) {
            $itemPostcode = $item->$postcodeField;
            //Tidy up the postcode, make uppercase and remove spaces
            $itemPostcode = str_replace(' ', '', $itemPostcode);
            $itemPostcode = strtoupper($itemPostcode);
            $length = strlen($itemPostcode);
            if($length > 4) {
                $trimmed = trim(substr(trim($itemPostcode), 0, -3));
                $outcode = GeoSorterPostcodes::where('area_code', '=', $trimmed)->first();
            }else{
                $outcome = GeoSorterPostcodes::where('area_code', '=', $itemPostcode)->first();
            }

            //Calculate the distance
            $calculator = new DistanceCalculator();
            $destination = new Coordinate\Decimal($outcode->lat, $outcode->long);
            $distance = $calculator->getDistance($source, $destination);

            $distanceItem = [
                'id' => $item->id,
                'distance' => $distance
            ];
            $distanceArray[] = $distanceItem;
        }

        /*
         * Sort the results by 'distance' in the user defined order.
         */
        // Check if PHP7 or above for spaceship operator
        if(defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 7) {
            uasort($distanceArray, function ($item1, $item2) use ($sortOrder) {
                if($sortOrder == 'SORT_ASC'){
                    return $item1['distance'] <=> $item2['distance'];
                }else{
                    return $item2['distance'] <=> $item1['distance'];
                }
            });
        }else{
            $sortedArray = [];
            foreach($distanceArray as $key => $row) {
                $sortedArray[$key] = $row['distance'];
            }
            array_multisort($sortedArray, $sortOrder, $distanceArray);
        }

        return $distanceArray;
    }
}
