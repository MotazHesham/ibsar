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
        // Alter enum to add new values
        DB::statement("ALTER TABLE dynamic_services MODIFY COLUMN category ENUM('training', 'assistance', 'social_programs', 'surgical_procedures', 'detection_center') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to original values
        DB::statement("ALTER TABLE dynamic_services MODIFY COLUMN category ENUM('training', 'assistance', 'social_programs') NULL");
    }
};
