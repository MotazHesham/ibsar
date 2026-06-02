<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_finance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_order_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('source');
            $table->nullableMorphs('reference');
            $table->string('workflow_category');
            $table->string('workflow_step')->nullable();
            $table->string('trigger_action');
            $table->string('title');
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('status', ['unposted', 'posted'])->default('unposted');
            $table->string('journal_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['beneficiary_order_id', 'workflow_category', 'trigger_action'],
                'workflow_finance_requests_unique_trigger'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_finance_requests');
    }
};
