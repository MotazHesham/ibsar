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
        Schema::create('dynamic_service_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_order_id');
            $table->unsignedBigInteger('dynamic_service_id');
            $table->json('field_data')->nullable(); // Store field metadata + values
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('beneficiary_order_id')->references('id')->on('beneficiary_orders')->onDelete('cascade');
            $table->foreign('dynamic_service_id')->references('id')->on('dynamic_services')->onDelete('cascade');
            
            $table->index(['beneficiary_order_id']);
            $table->index(['dynamic_service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_service_orders');
    }
};
