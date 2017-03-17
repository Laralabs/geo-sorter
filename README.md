# Geo Sorter for Laravel 5+
Postcode distance sorting package for Laravel Eloquent Collections.

**UNITED KINGDOM POSTCODES ONLY**

## Installation

Install via composer by adding the following line to your `composer.json` file inside `require`:
```php
"laralabs/geo-sorter": "~1.0.0"
```

After updating composer you will need to add the ServiceProvider to the providers array in `config/app.php`:
```php
Laralabs\GeoSorter\GeoSorterServiceProvider::class
```

Optionally, you can also add the Facade to the Aliases array:
```php
'GeoSorter' => Laralabs\GeoSorter\Facade\GeoSorterFacade::class
```

Once this has been added to your applications configuration file, run the following command to publish the config file to `config/geosorter.php` and migration file to `database/migrations/2017_03_16_000000_create_postcodes_table.php`:
```php
php artisan vendor:publish
```
The tags `config` and `migration` have been set if you wish to copy only one of the above items.

You should receive a confirmation that the files have been copied over successfully from artisan.

Create the postcodes table by running the following command:
```php
php artisan migrate
```
This table will get populated with UK Outcodes and their lat/long coordinates from the SQL file in package directory.

Once this has been done the configuration file located at `config/geosorter.php` needs to be altered to make sure it reflects your environment:

```php
<?php

return [
    'postcodeTable'     =>  'postcodes',
    'sortOrder'         =>  'SORT_ASC',
    'postcodeField'     =>  'postcode'
];
```

`postcodeTable` is the table where the UK Outcodes and their coordinates are stored, the migration will call this table `postcodes` by default.

`sortOrder` is the order in which the collection will be sorted, this should be set to `SORT_ASC` (Ascending) or `SORT_DESC` (Descending).

`postcodeField` is the name of the postcode field in your collection.

## Example

Here is a simple code sample on how this package would be used.

```php
<?php
use Laralabs\GeoSorter\GeoSorter;

$collection = Addresses::all();
$postcode   = 'B61 XYZ';

$collection = GeoSorter::geoSort($collection, $postcode);

```

The above code would sort the Addresses collection in distance from the given postcode `B61 XYZ`.

## Credits

[Ayeo/Geo](https://github.com/ayeo/geo) is used to calculate the distance between coordinates.

## Support

Please raise an issue on Github if there is a problem.

## License

This is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).