<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontAchievementsTable extends Migration
{
    public function up()
    {
        Schema::create('front_achievements', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->string('title');
            $table->string('achievement');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
