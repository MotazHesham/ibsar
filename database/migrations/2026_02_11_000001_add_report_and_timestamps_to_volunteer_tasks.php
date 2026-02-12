<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_tasks', function (Blueprint $table) {
            $table->datetime('started_at')->nullable()->after('notes');
            $table->datetime('finished_at')->nullable()->after('started_at');
            $table->longText('report')->nullable()->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('volunteer_tasks', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'finished_at', 'report']);
        });
    }
};
