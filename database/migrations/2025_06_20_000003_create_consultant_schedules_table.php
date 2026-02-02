<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultantSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('consultant_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consultant_id');
            $table->enum('day', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration'); // in minutes
            $table->enum('attendance_type', ['online', 'in_person']);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('consultant_id', 'consultant_schedules_consultant_id_fk')
                ->references('id')
                ->on('consultants')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultant_schedules');
    }
} 