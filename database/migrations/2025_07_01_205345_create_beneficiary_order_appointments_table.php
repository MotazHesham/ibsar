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
        Schema::create('beneficiary_order_appointments', function (Blueprint $table) {
            $table->id(); 
            $table->string('day');
            $table->date('date');
            $table->time('time');
            $table->integer('duration'); // in minutes
            $table->enum('attendance_type', ['online', 'in_person']);
            $table->string('status')->default('pending');
            $table->timestamps();
            
            $table->unsignedBigInteger('beneficiary_order_id')->nullable();
            $table->foreign('beneficiary_order_id', 'beneficiary_order_id_fk_10588501')->references('id')->on('beneficiary_orders'); 
            
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_id_fk_10242501')->references('id')->on('beneficiaries'); 

            $table->unsignedBigInteger('consultant_id')->nullable();
            $table->foreign('consultant_id', 'consultant_id_fk_10242501')->references('id')->on('consultants'); 

            $table->unsignedBigInteger('consultation_type_id')->nullable();
            $table->foreign('consultation_type_id', 'consultation_type_id_fk_10242501')->references('id')->on('consultation_types'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_order_appointments');
    }
};
