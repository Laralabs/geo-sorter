<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter;

use Illuminate\Database\Eloquent\Model;

class GeoSorterPostcodes extends Model
{
    /*
     * Get the postcodes table name from config
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = config('geosorter.postcode_table') ?? 'geo_sorter_postcodes';
    }

    /**
     * @var array
     */
    protected $fillable = [
        'area_code',
        'lat',
        'long'
    ];
}
