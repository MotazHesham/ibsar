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
        Schema::create('beneficiary_field_visibility_settings', function (Blueprint $table) {
            $table->id();
            $table->string('field_name')->unique(); // e.g., 'name', 'dob', 'phone', etc.
            $table->string('field_group'); // e.g., 'basic_information', 'work_information', etc.
            $table->string('field_label'); // Display name for admin interface
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_required')->default(false);
            $table->text('description')->nullable(); // Optional description for admin
            $table->integer('sort_order')->default(0); // For ordering fields in admin interface
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_field_visibility_settings');
    }
};
