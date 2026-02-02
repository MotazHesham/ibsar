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
        Schema::create('service_loan_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_loan_id')->nullable();
            $table->foreign('service_loan_id', 'service_loan_fk_102116')->references('id')->on('service_loans');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id', 'beneficiary_fk_1042216')->references('id')->on('beneficiaries'); 
            $table->string('status')->default('pending');
            $table->string('name');
            $table->string('identity_number');
            $table->string('member_position')->default('member')->nullable();
            
            $table->string('project_type')->nullable();
            $table->string('project_location')->nullable(); 
            $table->unsignedBigInteger('district_id')->nullable();
            $table->foreign('district_id', 'district_fk_105415116')->references('id')->on('districts');
            $table->string('street')->nullable(); 
            $table->date('project_start_date')->nullable();
            $table->string('project_years_of_experience')->nullable();
            $table->string('project_short_description')->nullable();
            $table->string('project_financial_source')->nullable();
            $table->string('purpose_of_loan')->nullable();
            $table->string('has_previous_loan')->nullable();
            $table->string('previous_loan_number')->nullable(); 
            $table->integer('installment')->nullable();
            $table->integer('months')->nullable();
            $table->decimal('amount', 10, 2)->nullable(); 
            $table->string('handle')->nullable();
            
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->foreign('loan_id', 'loan_fk_105415116')->references('id')->on('loans');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_loan_members');
    }
};
