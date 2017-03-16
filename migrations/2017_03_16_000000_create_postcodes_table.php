<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code');
            $table->double('lat');
            $table->double('long');
            $table->timestamps();
        });

        DB::unprepared(file_get_contents(__DIR__ . '/../../vendor/laralabs/geo-sorter/migrations/sql/uk_postcodes.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('postcodes');
    }
}
