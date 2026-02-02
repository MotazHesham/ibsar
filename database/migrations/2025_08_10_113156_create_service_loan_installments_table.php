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
        Schema::create('service_loan_installments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_loan_id')->nullable();
            $table->foreign('service_loan_id', 'service_loan_fk_114123')->references('id')->on('service_loans');
            $table->decimal('installment', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('installment_date');  
            $table->string('payment_status')->default('pending'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_loan_installments');
    }
};
