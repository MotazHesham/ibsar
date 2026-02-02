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
        Schema::create('service_loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('beneficiary_order_id')->nullable();
            $table->foreign('beneficiary_order_id', 'beneficiary_order_fk_10425116')->references('id')->on('beneficiary_orders');
            $table->string('group_name')->nullable();

            $table->decimal('amount', 10, 2)->nullable(); 
            $table->integer('installment')->nullable();
            $table->integer('months')->nullable();

            $table->string('kafil_name')->nullable();
            $table->string('kafil_identity_num')->nullable();
            $table->unsignedBigInteger('accommodation_type_id')->nullable();
            $table->foreign('accommodation_type_id', 'accommodation_type_fk_152452')->references('id')->on('accommodation_types');
            $table->unsignedBigInteger('marital_status_id')->nullable();
            $table->foreign('marital_status_id', 'marital_status_fk_1055123666')->references('id')->on('marital_statuses'); 
            $table->unsignedBigInteger('educational_qualification_id')->nullable();
            $table->foreign('educational_qualification_id', 'educational_qualification_fk_1024468')->references('id')->on('educational_qualifications');
            $table->unsignedBigInteger('job_type_id')->nullable();
            $table->foreign('job_type_id', 'job_type_fk_105343467')->references('id')->on('job_types');
            $table->unsignedBigInteger('kafil_district_id')->nullable();
            $table->foreign('kafil_district_id', 'kafil_district_fk_105415116')->references('id')->on('districts');
            $table->string('kafil_street')->nullable();
            $table->string('kafil_nearby_address')->nullable();
            $table->string('kafil_phone')->nullable();
            $table->string('kafil_phone2')->nullable();
            $table->string('kafil_work_phone')->nullable();
            $table->string('kafil_work_address')->nullable();
            $table->string('kafil_email')->nullable();
            $table->string('kafil_work_name')->nullable();
            $table->string('kafil_mail_box')->nullable();
            $table->string('kafil_postal_code')->nullable();

            $table->text('contacts')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_loans');
    }
};
