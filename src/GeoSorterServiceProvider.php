<?php

namespace Laralabs\GeoSorter;

use Illuminate\Support\ServiceProvider;

class GeoSorterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        /*
         * Check if Laravel version is greater than 5.0, vendor:publish not supported in 4.x
         */
        if(version_compare($app::VERSION, '5.0') >= 0) {
            $configPath     =   realpath(__DIR__ . '/../config/geosorter.php');
            $migrationPath  =   realpath(__DIR__ . '/../migrations/2017_03_16_000000_create_postcodes_table.php');

            /**
             * Copy configuration and migration files to appropriate directories when user runs php artisan vendor:publish
             */
            $this->publishes([
                $configPath     => config_path('geosorter.php')
            ], 'config');
            $this->publishes([
                $migrationPath  =>  database_path('/migrations/2017_03_16_000000_create_postcodes_table.php')
            ], 'migration');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
