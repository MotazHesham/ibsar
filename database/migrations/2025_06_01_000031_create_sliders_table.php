<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidersTable extends Migration
{
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->boolean('publish')->default(0)->nullable();
            $table->string('button_name')->nullable();
            $table->string('button_link')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
