<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longText('short_description');
            $table->longText('description');
            $table->string('attend_type');
            $table->string('certificate');
            $table->string('trainer');
            $table->date('start_at');
            $table->date('end_at');
            $table->boolean('published')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
