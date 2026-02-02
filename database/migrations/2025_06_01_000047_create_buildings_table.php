<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingsTable extends Migration
{
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('number_of_floors');
            $table->integer('number_of_apartments');
            $table->longText('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->longText('apartment_specifications');
            $table->longText('support_services')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
