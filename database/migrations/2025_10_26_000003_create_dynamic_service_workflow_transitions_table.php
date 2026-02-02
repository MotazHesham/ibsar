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
        Schema::create('dynamic_service_workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id');
            $table->string('from_status');
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id'); // Admin who made the transition
            $table->json('data')->nullable(); // Additional data for the transition
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('dynamic_service_workflows_base')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['workflow_id']);
            $table->index(['from_status', 'to_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_service_workflow_transitions');
    }
};

