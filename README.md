# Geo Sorter for Laravel
Postcode distance sorting package for Laravel Collections.

**UNITED KINGDOM POSTCODES ONLY**

## :rocket: Quick Start

### Installation
Require the package in the `composer.json` of your project.
```
composer require laralabs/geo-sorter
```
Publish the configuration file.
```
php artisan vendor:publish --tag=geosorter-config
```
Edit the configuration file and set your desired settings. If you want to use a custom database table name, set it here and cache your config before moving onto the next step.

Create the postcodes table by running the following command:
```php
php artisan migrate
```

Once the database table has been created, run the following command to populate it with the latest UK Postcode District data.
```php
php artisan geosorter:update
```

### Usage
A helper function and facade are available, choose your preferred method. The `sortByPostcode` method accepts three arguments, the third argument being an optional `$sort` which can be used to override the sort order defined in the config.

Facade:
```php
<?php
$collection = Addresses::all();
$postcode   = 'B61 XYZ';

$collection = GeoSorter::sortByPostcode($collection, $postcode, 'ASC');

```

Helper:
```php
<?php
$collection = Addresses::all();
$postcode   = 'B61 XYZ';

$collection = geo_sorter()->sortByPostcode($collection, $postcode);

```

The above code would sort the Addresses collection in distance from the given postcode `B61 XYZ`.

## :pushpin: Credits

[Ayeo/Geo](https://github.com/ayeo/geo) is used to calculate the distance between coordinates.

## :orange_book: Documentation
Full documentation can be found [on the docs website](https://docs.laralabs.uk/timezone/).

## :speech_balloon: Support
Please raise an issue on GitHub if there is a problem.

## :key: License
This is open-sourced software licensed under the [MIT License](http://opensource.org/licenses/MIT).