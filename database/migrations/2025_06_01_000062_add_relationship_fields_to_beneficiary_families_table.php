<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBeneficiaryFamiliesTable extends Migration
{
    public function up()
    {
        Schema::table('beneficiary_families', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_fk_10586727')->references('id')->on('beneficiaries');
            $table->unsignedBigInteger('family_relationship_id')->nullable();
            $table->foreign('family_relationship_id', 'family_relationship_fk_10586733')->references('id')->on('family_relationships');
            $table->unsignedBigInteger('marital_status_id')->nullable();
            $table->foreign('marital_status_id', 'marital_status_fk_10586734')->references('id')->on('marital_statuses');
            $table->unsignedBigInteger('health_condition_id')->nullable();
            $table->foreign('health_condition_id', 'health_condition_fk_10586735')->references('id')->on('health_conditions');
            $table->unsignedBigInteger('disability_type_id')->nullable();
            $table->foreign('disability_type_id', 'disability_type_fk_10586736')->references('id')->on('disability_types');
            $table->unsignedBigInteger('educational_qualification_id')->nullable();
            $table->foreign('educational_qualification_id', 'educational_qualification_fk_10586737')->references('id')->on('educational_qualifications');
        });
    }
}
