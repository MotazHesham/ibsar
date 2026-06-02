<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('dynamic_service_workflow_transitions');
        Schema::dropIfExists('dynamic_service_workflow_social_programs');
        Schema::dropIfExists('dynamic_service_workflow_training');
        Schema::dropIfExists('dynamic_service_workflow_assistance');
        Schema::dropIfExists('dynamic_service_workflows_base');
    }

    public function down(): void
    {
        // Workflow feature removed; tables are not recreated on rollback.
    }
};
