<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultantsTable extends Migration
{
    public function up()
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consultation_type_id');
            $table->string('name');
            $table->string('national_id');
            $table->string('phone_number');
            $table->string('academic_degree');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('consultation_type_id', 'consultants_consultation_type_id_fk')
                ->references('id')
                ->on('consultation_types')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultants');
    }
} 