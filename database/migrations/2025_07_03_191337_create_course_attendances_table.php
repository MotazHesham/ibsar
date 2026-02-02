<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_attendances', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->date('date'); 
            $table->unsignedBigInteger('course_student_id')->nullable();
            $table->foreign('course_student_id', 'course_student_fk_105252501')->references('id')->on('course_students'); 
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreign('course_id', 'course_fk_10455201')->references('id')->on('courses');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_attendances');
    }
};
