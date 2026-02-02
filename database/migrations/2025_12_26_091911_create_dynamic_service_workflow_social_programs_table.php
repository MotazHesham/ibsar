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
        Schema::create('dynamic_service_workflow_social_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id');
            $table->boolean('program_accepted')->nullable();
            $table->boolean('program_completed')->nullable();
            $table->integer('waiting_list_position')->nullable();
            $table->boolean('document_prepared')->nullable();
            $table->json('document_data')->nullable();
            $table->string('executive_decision')->nullable(); // 'accepted' or 'refused'
            $table->text('executive_decision_notes')->nullable();
            $table->boolean('program_proceeded')->nullable();
            $table->dateTime('program_proceeded_date')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('dynamic_service_workflows_base')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_service_workflow_social_programs');
    }
};
