<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter;

use Ayeo\Geo\Coordinate\Decimal;
use Ayeo\Geo\DistanceCalculator;
use Illuminate\Support\Collection;
use Laralabs\GeoSorter\Exceptions\InvalidFieldException;
use Laralabs\GeoSorter\Exceptions\InvalidPostcodeException;
use Laralabs\GeoSorter\Exceptions\InvalidUnitException;
use Laralabs\GeoSorter\Exceptions\PostcodeNotFoundException;
use Laralabs\GeoSorter\Models\GeoSorterPostcodes;

class GeoSorter
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $sortOrder;

    /**
     * @var int
     */
    protected $radius;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * @throws InvalidFieldException
     */
    public function __construct()
    {
        $this->field = config('geosorter.postcode_field');
        $this->sortOrder = config('geosorter.sort_order');
        $this->radius = config('geosorter.distance_radius') ?? 0;
        $this->unit = config('geosorter.distance_unit') ?? 'miles';
        $this->strict = config('geosorter.strict_mode') ?? true;

        if (!is_string($this->field)) {
            throw new InvalidFieldException($this->field);
        }
    }

    /**
     * sortByPostcode
     *
     * Sorts given collection based upon distance from the
     * given postcode, returns sorted collection.
     *
     * @param Collection $collection
     * @param string $postcode
     * @param string|null $sort
     * @return Collection|null
     * @throws PostcodeNotFoundException|InvalidUnitException|InvalidPostcodeException
     */
    public function sortByPostcode($collection, $postcode, $sort = null): ?Collection
    {
        if ($this->strict && preg_match('/^([A-Za-z][A-Ha-hJ-Yj-y]?[0-9][A-Za-z0-9]? ?[0-9][A-Za-z]{2}|[Gg][Ii][Rr] ?0[Aa]{2})$/', $postcode) === 0) {
            throw new InvalidPostcodeException($postcode);
        }

        /*
         * Calculate distance radius in metres
         */
        if($this->radius > 0){
            switch ($this->unit) {
                case 'metres':
                    //Metres is what we want, do nothing
                    break;
                case 'kilometres':
                    $this->radius *= 1000;
                    break;
                case 'miles':
                    $this->radius *= 1609.34;
                    break;
                default:
                    throw new InvalidUnitException($this->unit);
                    break;
            }
        }

        /*
         * Make postcode uppercase and remove spaces
         */
        $postcode = str_replace(' ', '', strtoupper($postcode));

        /*
         * Check length of postcode to see if we are just dealing with
         * an outcode.
         */
        $length = strlen($postcode);

        if($length > 4) {
            $sourceOutcode = GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($postcode),0,-3)))->first();
        }else{
            $sourceOutcode = GeoSorterPostcodes::where('area_code', '=', $postcode)->first();
        }

        if (!$sourceOutcode instanceof GeoSorterPostcodes) {
            throw new PostcodeNotFoundException();
        }

        /*
         * Create Decimal object from source postcode latitude and longitude
         * coordinates.
         */
        $source = new Decimal($sourceOutcode->lat, $sourceOutcode->long);

        /*
         * Iterate through collection, work out item postcode distance from
         * source, map and populate 'distance' property on collection item.
         */
        $mapped = $collection->map(function ($item) use ($source) {

            $field = $this->field;
            $itemPostcode = $item->$field;
            
            if ($this->strict && preg_match('/^([A-Za-z][A-Ha-hJ-Yj-y]?[0-9][A-Za-z0-9]? ?[0-9][A-Za-z]{2}|[Gg][Ii][Rr] ?0[Aa]{2})$/', $itemPostcode) === 0) {
                throw new InvalidPostcodeException($itemPostcode);
            }

            /*
             * Make postcode uppercase and remove spaces
             */
            $itemPostcode = str_replace(' ', '', strtoupper($itemPostcode));

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

            if ($outcode !== null && $source instanceof Decimal) {
                /*
                 * Calculate the distance in metres
                 */
                $calculator = new DistanceCalculator();
                $destination = new Decimal($outcode->lat, $outcode->long);
                $distance = $calculator->getDistance($source, $destination);
                $item['distance'] = $distance;
            } else {
                $item['distance'] = -1;
            }

            return $item;

        });

        /*
         * Apply radius filter if it is set
         */
        if($this->radius > 0) {
            $mapped = $mapped->filter(function($item) {
                return $item->distance <= $this->radius;
            })->values();
        }

        /*
         * Sort the collection by 'distance' in the user defined order.
         */
        if ($sort !== null && in_array($sort, ['ASC', 'DESC'])) {
            $this->sortOrder = $sort;
        }

        $mapped = $this->sortOrder === 'DESC' ? $mapped->sortByDesc('distance') : $mapped->sortBy('distance');

        return $mapped;
    }
}
