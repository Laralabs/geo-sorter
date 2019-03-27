<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Postcode Table
    |--------------------------------------------------------------------------
    |
    | The name of the table used to store postcode district data, change this 
    | before running migrations if you want to change to something different.
    |
    */
    'postcode_table' => 'geo_sorter_postcodes',

    /*
    |--------------------------------------------------------------------------
    | Sort Order
    |--------------------------------------------------------------------------
    |
    | Set the default sort order here, accepted values are 'ASC' or 'DESC'.
    |
    */
    'sort_order' => 'ASC',

    /*
    |--------------------------------------------------------------------------
    | Postcode Field
    |--------------------------------------------------------------------------
    |
    | This is the field that will be searched for on items in the collection.
    |
    */
    'postcode_field' => 'postcode',

    /*
    |--------------------------------------------------------------------------
    | Distance Radius
    |--------------------------------------------------------------------------
    |
    | Set this to the distance you want items in the collection to be filered
    | by, unit of measure is defined in the Distance Unit below.
    |
    | If set to 0 then the filter will not be applied.
    |
    */
    'distance_radius' => 0,

    /*
    |--------------------------------------------------------------------------
    | Distance Unit
    |--------------------------------------------------------------------------
    |
    | Set this to the unit of measurement for Distance Radius, it supports the
    | following: metres, kilometres or miles.
    |
    */
    'distance_unit' => 'miles',

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | Set this to true if you want the package to throw exceptions if it
    | encounters and invalid postcode in the collection, if false the item will
    | be skipped and it's distance set to -1.
    |
    */
    'strict_mode' => true,
];