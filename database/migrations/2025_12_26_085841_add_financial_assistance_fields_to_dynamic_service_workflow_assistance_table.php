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
        Schema::table('dynamic_service_workflow_assistance', function (Blueprint $table) {
            $table->text('financial_feedback_data')->nullable()->after('feedback_data');
            $table->text('study_case_rejection_reason')->nullable()->after('financial_feedback_data');
            $table->json('missing_data_info')->nullable()->after('study_case_rejection_reason');
            $table->string('financial_receipt_file')->nullable()->after('missing_data_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_service_workflow_assistance', function (Blueprint $table) {
            $table->dropColumn([
                'financial_feedback_data',
                'study_case_rejection_reason',
                'missing_data_info',
                'financial_receipt_file',
            ]);
        });
    }
};
