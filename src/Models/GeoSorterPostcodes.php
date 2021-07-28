<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter\Models;

use Illuminate\Database\Eloquent\Model;

class GeoSorterPostcodes extends Model
{
    /** @var string[] */
    protected $fillable = [
        'area_code',
        'lat',
        'long'
    ];

    /** @var string */
    protected $table = 'geo_sorter_postcodes';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('geosorter.postcode_table') ?? 'geo_sorter_postcodes';
    }
}
