<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoSorterPostcodesTable extends Migration
{
    /** @var string */
    protected $table;

    public function __construct()
    {
        $this->table = config('geosorter.postcode_table') ?? 'geo_sorter_postcodes';
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table): void {
            $table->increments('id');
            $table->string('area_code')->unique();
            $table->decimal('lat', 12, 8);
            $table->decimal('long', 12, 8);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop($this->table);
    }
}
