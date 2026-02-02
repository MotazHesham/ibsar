<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityDistrictPivotTable extends Migration
{
    public function up()
    {
        Schema::create('city_district', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id', 'city_id_fk_10586377')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id', 'district_id_fk_10586377')->references('id')->on('districts')->onDelete('cascade');
        });
    }
}
