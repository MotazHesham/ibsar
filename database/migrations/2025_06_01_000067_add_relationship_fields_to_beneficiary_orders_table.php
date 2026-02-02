<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBeneficiaryOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('beneficiary_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_fk_10589840')->references('id')->on('beneficiaries');
            $table->unsignedBigInteger('beneficiary_family_id')->nullable();
            $table->foreign('beneficiary_family_id', 'beneficiary_family_fk_415213')->references('id')->on('beneficiary_families');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id', 'service_fk_10589841')->references('id')->on('services');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id', 'status_fk_10589889')->references('id')->on('service_statuses');
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->foreign('specialist_id', 'specialist_fk_10595304')->references('id')->on('users');
        });
    }
}
