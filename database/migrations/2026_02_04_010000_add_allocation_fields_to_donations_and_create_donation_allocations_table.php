<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->decimal('used_amount', 12, 2)->default(0)->after('total_amount');
            $table->decimal('remaining_amount', 12, 2)->default(0)->after('used_amount');
        });

        // Initialize remaining_amount for existing donations
        DB::table('donations')->update([
            'remaining_amount' => DB::raw('total_amount - used_amount'),
        ]);

        Schema::create('donation_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->foreignId('beneficiary_order_id')->constrained('beneficiary_orders')->cascadeOnDelete();
            $table->decimal('allocated_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_allocations');

        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['used_amount', 'remaining_amount']);
        });
    }
};

