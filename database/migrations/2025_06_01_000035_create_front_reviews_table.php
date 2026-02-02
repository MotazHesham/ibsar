<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('front_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('review')->nullable();
            $table->integer('rate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
