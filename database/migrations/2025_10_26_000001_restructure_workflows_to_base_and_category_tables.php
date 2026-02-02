<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Create new base table structure
        Schema::create('dynamic_service_workflows_base', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dynamic_service_order_id');
            $table->string('category'); // training, assistance, etc.
            $table->string('current_status')->default('pending_review');
            $table->text('notes')->nullable();
            $table->text('refused_reason')->nullable();
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dynamic_service_order_id')->references('id')->on('dynamic_service_orders')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['dynamic_service_order_id']);
            $table->index(['current_status']);
            $table->index(['category']);
        });

        // Step 2: Create training-specific table
        Schema::create('dynamic_service_workflow_training', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id')->unique();
            $table->string('service_type')->nullable(); // 'individual' or 'group'
            $table->dateTime('appointment_date')->nullable();
            $table->boolean('appointment_attended')->nullable();
            $table->text('specialist_report')->nullable();
            $table->boolean('training_department_approved')->nullable();
            $table->dateTime('program_start_date')->nullable();
            $table->json('attendance_data')->nullable();
            $table->json('accounting_entries')->nullable();
            $table->boolean('test_passed')->nullable();
            $table->text('test_result')->nullable();
            $table->text('alternatives_offered')->nullable();
            $table->json('satisfaction_assessment')->nullable();
            $table->boolean('device_delivered')->nullable();
            $table->unsignedBigInteger('device_item_id')->nullable();
            // Group-specific fields
            $table->text('payment_url')->nullable();
            $table->boolean('is_paid_program')->default(false);
            $table->boolean('in_waiting_list')->default(false);
            $table->integer('group_position')->nullable();
            $table->integer('group_size')->nullable();
            $table->boolean('group_completed')->default(false);
            $table->json('meeting_schedule')->nullable();
            $table->boolean('certificate_issued')->default(false);
            $table->boolean('certificate_test_passed')->nullable();
            $table->text('certificate_message_sent')->nullable();
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('dynamic_service_workflows_base')->onDelete('cascade');
        });

        // Step 3: Create assistance-specific table
        Schema::create('dynamic_service_workflow_assistance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id')->unique();
            $table->string('assistance_type')->nullable(); // 'real_receipt' or 'financial'
            $table->boolean('study_case_approved')->nullable();
            $table->boolean('stock_available')->nullable();
            $table->unsignedBigInteger('stock_item_id')->nullable();
            $table->boolean('need_training')->nullable();
            $table->json('receiving_form_data')->nullable();
            $table->boolean('review_request_sent')->default(false);
            // Training-related fields (for assistance that needs training)
            $table->json('training_schedule')->nullable();
            $table->dateTime('training_program_start_date')->nullable();
            $table->json('training_attendance_data')->nullable();
            $table->json('training_financial_statements')->nullable();
            $table->boolean('training_test_passed')->nullable();
            $table->text('training_test_notes')->nullable();
            $table->unsignedBigInteger('machine_item_id')->nullable();
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('dynamic_service_workflows_base')->onDelete('cascade');
        }); 
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new tables
        Schema::dropIfExists('dynamic_service_workflow_assistance');
        Schema::dropIfExists('dynamic_service_workflow_training');
        Schema::dropIfExists('dynamic_service_workflows_base'); 
    }
};

