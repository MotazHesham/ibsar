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
        Schema::create('service_loan_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_loan_id')->nullable();
            $table->foreign('service_loan_id', 'service_loan_fk_152323')->references('id')->on('service_loans');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference_number')->nullable();
            $table->date('paid_date')->nullable();
            $table->text('note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('handle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_loan_payments');
    }
};
