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
use Throwable;

class GeoSorter
{
    /** @var string */
    protected $field;

    /** @var string */
    protected $sortOrder;

    /** @var int */
    protected $radius;

    /** @var string */
    protected $unit;

    /** @var bool */
    protected $strict;

    /** @var DistanceCalculator */
    protected $calculator;

    /** @throws InvalidFieldException */
    public function __construct()
    {
        $this->field = config('geosorter.postcode_field');
        $this->sortOrder = config('geosorter.sort_order');
        $this->radius = config('geosorter.distance_radius') ?? 0;
        $this->unit = config('geosorter.distance_unit') ?? 'miles';
        $this->strict = config('geosorter.strict_mode') ?? true;
        $this->calculator = new DistanceCalculator();

        throw_if(is_string($this->field) === false, new InvalidFieldException($this->field));
    }

    /**
     * sortByPostcode
     *
     * Sorts given collection based upon distance from the
     * given postcode, returns sorted collection.
     *
     * @throws PostcodeNotFoundException|InvalidUnitException|InvalidPostcodeException|Throwable
     */
    public function sortByPostcode(Collection $collection, string $postcode, ?string $sort = null): ?Collection
    {
        $this->validatePostcode($postcode);

        /*
         * Calculate distance radius in metres
         */
        if ($this->radius > 0) {
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
            }
        }

        /*
         * Make postcode uppercase and remove spaces
         */
        $postcode = $this->formatPostcode($postcode);

        /*
         * Check length of postcode to see if we are just dealing with
         * an outcode.
         */
        $sourceOutcode = $this->getOutcode($postcode);

        throw_if($sourceOutcode instanceof GeoSorterPostcodes === false, new PostcodeNotFoundException());

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

            $this->validatePostcode($itemPostcode);

            /*
             * Make postcode uppercase and remove spaces
             */
            $itemPostcode = $this->formatPostcode($itemPostcode);

            /*
             * Check length of postcode to see if we are just dealing with
             * an outcode.
             */
            $outcode = $this->getOutcode($itemPostcode);

            if ($outcode !== null && $source instanceof Decimal) {
                /*
                 * Calculate the distance in metres
                 */
                $destination = new Decimal($outcode->lat, $outcode->long);
                $item['distance'] = $this->calculator->getDistance($source, $destination);
            } else {
                $item['distance'] = -1;
            }

            return $item;
        });

        /*
         * Apply radius filter if it is set
         */
        if ($this->radius > 0) {
            $mapped = $mapped->filter(function ($item): bool {
                return $item->distance <= $this->radius;
            })->values();
        }

        /*
         * Sort the collection by 'distance' in the user defined order.
         */
        if ($sort !== null && in_array($sort, ['ASC', 'DESC'])) {
            $this->sortOrder = $sort;
        }

        return $this->sortOrder === 'DESC' ? $mapped->sortByDesc('distance') : $mapped->sortBy('distance');
    }

    private function formatPostcode(string $postcode): string
    {
        return str_replace(' ', '', strtoupper($postcode));
    }

    private function getOutcode(string $postcode): ?GeoSorterPostcodes
    {
        if (strlen($postcode) > 4) {
            return GeoSorterPostcodes::where('area_code', '=', trim(substr(trim($postcode), 0, -3)))
                ->first();
        }

        return GeoSorterPostcodes::where('area_code', '=', $postcode)->first();
    }

    private function validatePostcode(string $postcode): void
    {
        throw_if(
            $this->strict && preg_match(
                '/^([A-Za-z][A-Ha-hJ-Yj-y]?[0-9][A-Za-z0-9]? ?[0-9][A-Za-z]{2}|[Gg][Ii][Rr] ?0[Aa]{2})$/',
                $postcode
            ) === 0,
            new InvalidPostcodeException($postcode)
        );
    }
}
