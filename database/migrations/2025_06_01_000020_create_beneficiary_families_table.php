<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryFamiliesTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiary_families', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identity_num')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('custom_health_condition')->nullable();
            $table->string('custom_disability_type')->nullable();
            $table->string('can_work')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
