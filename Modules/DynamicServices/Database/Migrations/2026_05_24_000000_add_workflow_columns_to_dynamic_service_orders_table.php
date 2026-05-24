<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dynamic_service_orders', function (Blueprint $table) {
            $table->string('workflow_step')->nullable()->after('field_data');
            $table->unsignedTinyInteger('approval_stage')->default(0)->after('workflow_step');
            $table->json('workflow_data')->nullable()->after('approval_stage');
        });
    }

    public function down(): void
    {
        Schema::table('dynamic_service_orders', function (Blueprint $table) {
            $table->dropColumn(['workflow_step', 'approval_stage', 'workflow_data']);
        });
    }
};
