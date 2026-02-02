<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBeneficiariesTable extends Migration
{
    public function up()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_10586659')->references('id')->on('users');
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->foreign('nationality_id', 'nationality_fk_10586665')->references('id')->on('nationalities');
            $table->unsignedBigInteger('marital_status_id')->nullable();
            $table->foreign('marital_status_id', 'marital_status_fk_10586666')->references('id')->on('marital_statuses');
            $table->unsignedBigInteger('job_type_id')->nullable();
            $table->foreign('job_type_id', 'job_type_fk_10586667')->references('id')->on('job_types');
            $table->unsignedBigInteger('educational_qualification_id')->nullable();
            $table->foreign('educational_qualification_id', 'educational_qualification_fk_10586668')->references('id')->on('educational_qualifications');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreign('region_id', 'region_fk_1058476')->references('id')->on('regions');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id', 'city_fk_1052276')->references('id')->on('cities');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->foreign('district_id', 'district_fk_10586676')->references('id')->on('districts');
            $table->unsignedBigInteger('health_condition_id')->nullable();
            $table->foreign('health_condition_id', 'health_condition_fk_10586682')->references('id')->on('health_conditions');
            $table->unsignedBigInteger('disability_type_id')->nullable();
            $table->foreign('disability_type_id', 'disability_type_fk_10586693')->references('id')->on('disability_types');
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->foreign('specialist_id', 'specialist_fk_10595483')->references('id')->on('users');
        });
    }
}
