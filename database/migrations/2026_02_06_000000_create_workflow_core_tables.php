<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create core tables for the generic, configuration-driven Workflow Engine.
     *
     * NOTE: These tables are intentionally generic and MUST NOT contain
     * any module-specific or legacy dynamic service workflow logic.
     */
    public function up(): void
    {
        /**
         * Table: workflows
         *
         * Defines a reusable workflow definition (e.g. training_program, social_aid).
         * This is a type-level definition, not a running instance.
         */
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            // Machine-readable key, e.g. "training_program", "social_aid"
            $table->string('key')->unique()->comment('Unique key for this workflow definition (e.g. training_program, social_aid)');
            $table->string('name')->comment('Human readable name of the workflow');
            $table->text('description')->nullable()->comment('Optional description of the workflow purpose');

            // Target entity type this workflow is designed for (e.g. App\\Models\\DynamicServiceOrder)
            $table->string('entity_type')->nullable()->comment('Fully qualified model class this workflow is typically attached to');

            // Optional semantic version for config management
            $table->string('version')->default('1.0')->comment('Workflow definition version');

            // Whether this workflow is available for new instances
            $table->boolean('is_active')->default(true)->comment('Whether this workflow is active for new instances');

            // Optional JSON configuration for advanced engine behavior (step metadata, ui hints, etc.)
            $table->json('config')->nullable()->comment('Arbitrary JSON configuration for the workflow definition');

            $table->timestamps();
        });

        /**
         * Table: workflow_steps
         *
         * Defines ordered steps inside a workflow.
         * Supported types: human, decision, auto, waiting.
         */
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')
                ->constrained('workflows')
                ->onDelete('cascade');

            // Machine-readable step key, e.g. "submit_request", "initial_review"
            $table->string('key')->comment('Unique key of the step within its workflow');
            $table->string('name')->comment('Human readable name of the step');

            // Step type strategy
            $table->enum('type', ['human', 'decision', 'auto', 'waiting'])
                ->default('human')
                ->comment('Execution strategy type: human, decision, auto, waiting');

            // Order of the step inside the workflow (for display and default traversal)
            $table->unsignedInteger('position')->default(0)->comment('Ordering of the step within the workflow');

            // Flags for start / end steps
            $table->boolean('is_start')->default(false)->comment('Whether this is the initial step of the workflow');
            $table->boolean('is_end')->default(false)->comment('Whether this is a terminal step of the workflow');

            // Which roles can act on this step (array of role IDs or slugs)
            $table->json('allowed_roles')->nullable()->comment('Array of role identifiers allowed to act on this step');

            // Optional policy ability to check (e.g. "approve", "review")
            $table->string('policy_ability')->nullable()->comment('Optional Laravel policy ability that guards executing this step');

            // Step-level configuration (form schema, auto-execution options, SLA, etc.)
            $table->json('config')->nullable()->comment('Arbitrary JSON configuration for this step');

            $table->timestamps();

            $table->unique(['workflow_id', 'key'], 'workflow_steps_workflow_id_key_unique');
        });

        /**
         * Table: workflow_transitions
         *
         * Defines allowed transitions between steps and the conditions under which they fire.
         */
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_id')
                ->constrained('workflows')
                ->onDelete('cascade');

            $table->foreignId('from_step_id')
                ->constrained('workflow_steps')
                ->onDelete('cascade');

            $table->foreignId('to_step_id')
                ->constrained('workflow_steps')
                ->onDelete('cascade');

            $table->string('name')->nullable()->comment('Optional name/label of the transition (e.g. Approve, Reject)');

            // Simple key for quick matching (e.g. "approved", "rejected", "completed").
            // This is usually mapped from a user action button.
            $table->string('condition_key')->nullable()->comment('Simple condition key like approved, rejected, completed');

            /**
             * Optional expression for complex conditions, evaluated against the instance context
             * (payload, entity attributes, etc.). The expression language is a small, safe DSL
             * implemented in the ConditionEvaluator (no PHP eval).
             *
             * Example: "beneficiaries_count >= required_capacity && stock_available == true"
             */
            $table->text('condition_expression')->nullable()->comment('Optional boolean expression evaluated by the ConditionEvaluator');

            // Whether this transition is the default fallback if no condition matches.
            $table->boolean('is_default')->default(false)->comment('Whether this transition is the default if no other condition matches');

            // Whether this transition can be used as a loopback to a previous step
            $table->boolean('is_loopback')->default(false)->comment('Marks this transition as a loopback to a previous step');

            // Arbitrary metadata about the transition (e.g. UI hints)
            $table->json('metadata')->nullable()->comment('Additional JSON metadata for this transition');

            $table->timestamps();

            $table->index(['workflow_id', 'from_step_id'], 'workflow_transitions_from_idx');
        });

        /**
         * Table: workflow_instances
         *
         * Represents a running instance of a workflow for a specific entity.
         */
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_id')
                ->constrained('workflows')
                ->onDelete('restrict');

            // Polymorphic relation to the business entity (e.g. DynamicServiceOrder, BeneficiaryOrder)
            $table->string('entity_type')->comment('Morph class name of the related business entity');
            $table->unsignedBigInteger('entity_id')->comment('Primary key of the related business entity');

            $table->foreignId('current_step_id')
                ->nullable()
                ->constrained('workflow_steps')
                ->onDelete('set null');

            // Status of this workflow instance
            $table->enum('status', ['running', 'completed', 'cancelled', 'on_hold'])
                ->default('running')
                ->comment('Lifecycle status of the workflow instance');

            // User who started this instance (if any)
            $table->unsignedBigInteger('started_by_id')->nullable()->comment('User who started the workflow instance');
            // User who completed/cancelled this instance (if any)
            $table->unsignedBigInteger('finished_by_id')->nullable()->comment('User who completed or cancelled the workflow instance');

            // Arbitrary state data associated with the instance (normalized or denormalized as needed)
            $table->json('data')->nullable()->comment('JSON payload with instance-level state, cached calculations, etc.');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id'], 'workflow_instances_entity_idx');
        });

        /**
         * Table: workflow_logs
         *
         * Full audit trail of all workflow actions (human or system).
         */
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_instance_id')
                ->constrained('workflow_instances')
                ->onDelete('cascade');

            $table->foreignId('workflow_id')
                ->constrained('workflows')
                ->onDelete('cascade');

            $table->foreignId('step_id')
                ->nullable()
                ->constrained('workflow_steps')
                ->onDelete('set null');

            // From / to step for transition events
            $table->unsignedBigInteger('from_step_id')->nullable()->comment('Source step of the transition, if applicable');
            $table->unsignedBigInteger('to_step_id')->nullable()->comment('Destination step of the transition, if applicable');

            // Action label (e.g. started, approved, rejected, auto_executed, moved)
            $table->string('action')->comment('Action name recorded in this log entry');

            // Who performed the action (human or system; null = system)
            $table->nullableMorphs('performer'); // performer_type, performer_id

            // Role used for this action (cached at log time)
            $table->string('performer_role')->nullable()->comment('Role name/identifier used to perform this action');

            // Optional free text or structured JSON metadata
            $table->text('notes')->nullable()->comment('Optional free text notes or comments for this action');
            $table->json('metadata')->nullable()->comment('Additional structured metadata about this action');

            $table->timestamps();

            $table->index(['workflow_instance_id', 'created_at'], 'workflow_logs_instance_created_idx');
        });

        /**
         * Table: workflow_step_documents (optional)
         *
         * Defines required / optional documents or templates attached to a step.
         */
        Schema::create('workflow_step_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_step_id')
                ->constrained('workflow_steps')
                ->onDelete('cascade');

            $table->string('name')->comment('Human readable document name (e.g. Training contract, ID copy)');
            $table->string('code')->nullable()->comment('Machine-readable document code');

            // Template identifier (e.g. view name, storage path, or external template key)
            $table->string('template_key')->nullable()->comment('Identifier used by the application to generate or fetch this document');

            $table->boolean('is_required')->default(true)->comment('Whether this document is required to complete the step');

            $table->json('config')->nullable()->comment('Additional JSON configuration (e.g. signatories, generation rules)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_step_documents');
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('workflow_instances');
        Schema::dropIfExists('workflow_transitions');
        Schema::dropIfExists('workflow_steps');
        Schema::dropIfExists('workflows');
    }
};

