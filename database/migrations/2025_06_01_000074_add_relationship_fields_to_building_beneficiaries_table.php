<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBuildingBeneficiariesTable extends Migration
{
    public function up()
    {
        Schema::table('building_beneficiaries', function (Blueprint $table) {
            $table->unsignedBigInteger('building_id')->nullable();
            $table->foreign('building_id', 'building_fk_10595475')->references('id')->on('buildings');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_fk_10595476')->references('id')->on('beneficiaries');
        });
    }
}
