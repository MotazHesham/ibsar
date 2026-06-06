<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('file_category')->nullable()->after('profile_status');
            $table->string('data_form_template')->nullable()->after('file_category');
            $table->string('gender')->nullable()->after('data_form_template');
            $table->unsignedInteger('children_count')->nullable()->default(0)->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['file_category', 'data_form_template', 'gender', 'children_count']);
        });
    }
};
