<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laralabs\GeoSorter\Exceptions\UpdateFailedException;
use Laralabs\GeoSorter\GeoSorterPostcodes;

class UpdatePostcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geosorter:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates geo sorter postcode district data from doogal.co.uk';

    /**
     * @var string
     */
    protected $url = 'https://www.doogal.co.uk/PostcodeDistrictsCSV.ashx';

    /**
     * Execute the console command.
     *
     * @throws UpdateFailedException
     */
    public function handle()
    {
        $handle = @fopen($this->url, 'r');

        if (!$handle) {
            throw new UpdateFailedException();
        }

        DB::table(config('geosorter.postcode_table'))->truncate();

        $i = 0;

        while (!feof($handle)) {
            $line = fgetcsv($handle);

            // Skip first line and only create record if there are active postcodes.
            if ($i > 0 && $line[9] != 0) {
                GeoSorterPostcodes::create([
                    'area_code' => $line[0],
                    'lat' => $line[1],
                    'long' => $line[2]
                ]);
            }

            $i++;
        }

        fclose($handle);
    }
}