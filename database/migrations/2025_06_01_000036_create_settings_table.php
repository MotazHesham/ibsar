<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key'); 
            $table->string('name')->nullable(); 
            $table->longText('options')->nullable(); 
            $table->longText('value')->nullable();
            $table->string('lang')->nullable();
            $table->string('type')->nullable();
            $table->string('group_name')->nullable();
            $table->integer('order_level')->nullable();
            $table->string('grid_col')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
