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
            $table->integer('waiting_list_position')->nullable()->after('machine_item_id');
            $table->json('vendor_offers')->nullable()->after('waiting_list_position');
            $table->integer('selected_vendor_id')->nullable()->after('vendor_offers');
            $table->text('management_decision_notes')->nullable()->after('selected_vendor_id');
            $table->string('payment_receipt_url')->nullable()->after('management_decision_notes');
            $table->json('feedback_data')->nullable()->after('payment_receipt_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_service_workflow_assistance', function (Blueprint $table) {
            $table->dropColumn([
                'waiting_list_position',
                'vendor_offers',
                'selected_vendor_id',
                'management_decision_notes',
                'payment_receipt_url',
                'feedback_data',
            ]);
        });
    }
};
