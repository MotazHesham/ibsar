<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityRegionPivotTable extends Migration
{
    public function up()
    {
        Schema::create('city_region', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id', 'region_id_fk_10586376')->references('id')->on('regions')->onDelete('cascade');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id', 'city_id_fk_10586376')->references('id')->on('cities')->onDelete('cascade');
        });
    }
}
