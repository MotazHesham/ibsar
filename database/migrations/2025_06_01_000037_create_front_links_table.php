<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontLinksTable extends Migration
{
    public function up()
    {
        Schema::create('front_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('link');
            $table->string('position');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
