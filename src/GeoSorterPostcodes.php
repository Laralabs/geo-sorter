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

use Illuminate\Database\Eloquent\Model;

class GeoSorterPostcodes extends Model
{
    /*
     * Get the postcodes table name from config
     */
    public function __construct()
    {
        parent::__construct();
        return $this->table = config('geosorter.postcodeTable');
    }
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;
}
