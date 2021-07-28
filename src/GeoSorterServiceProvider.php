<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter;

use Illuminate\Support\ServiceProvider;
use Laralabs\GeoSorter\Commands\UpdatePostcodes;

class GeoSorterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/geosorter.php', 'geosorter'
        );

        $this->commands([
            UpdatePostcodes::class
        ]);

        $this->app->bind('geosorter', function (): GeoSorter {
            return new GeoSorter();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/geosorter.php'  => config_path('geosorter.php'),
        ], 'geosorter-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
