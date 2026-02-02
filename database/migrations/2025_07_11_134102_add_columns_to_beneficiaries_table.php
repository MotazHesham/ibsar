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
        Schema::table('beneficiaries', function (Blueprint $table) { 
            $table->date('martial_status_date')->nullable();
            $table->text('job_details')->nullable();
            $table->text('case_study')->nullable();
            $table->unsignedBigInteger('accommodation_type_id')->nullable();
            $table->foreign('accommodation_type_id', 'accommodation_type_fk_1518707')->references('id')->on('accommodation_types');
            $table->unsignedBigInteger('accommodation_entity_id')->nullable();
            $table->foreign('accommodation_entity_id', 'accommodation_entity_fk_1513558')->references('id')->on('accommodation_entities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            //
        });
    }
};
