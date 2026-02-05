<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_allocation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_allocation_id')->constrained('donation_allocations')->cascadeOnDelete();
            $table->foreignId('donation_item_id')->constrained('donation_items')->cascadeOnDelete();
            $table->decimal('allocated_quantity', 10, 2);
            $table->decimal('allocated_amount', 12, 2); // quantity * unit_price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_allocation_items');
    }
};
