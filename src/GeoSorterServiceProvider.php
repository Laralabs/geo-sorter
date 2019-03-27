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
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/geosorter.php', 'geosorter'
        );

        $this->commands([
            UpdatePostcodes::class
        ]);

        $this->app->bind('geosorter', function ($app) {
            return new GeoSorter();
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/geosorter.php'  => config_path('geosorter.php'),
        ], 'geosorter-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
