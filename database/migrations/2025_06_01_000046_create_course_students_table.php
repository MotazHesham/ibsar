<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('course_students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('note')->nullable();
            $table->boolean('certificate')->default(0);
            $table->boolean('transportation')->default(0);
            $table->boolean('prev_experience')->default(0);
            $table->boolean('attend_same_course_before')->default(0);
            $table->longText('prev_courses')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
