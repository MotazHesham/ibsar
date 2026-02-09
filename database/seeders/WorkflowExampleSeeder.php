<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Seeds workflow definitions from example JSON files.
 *
 * Run after migrations: php artisan db:seed --class=WorkflowExampleSeeder
 *
 * Example files: config/workflows/examples/*.json
 */
class WorkflowExampleSeeder extends Seeder
{
    public function run(): void
    {
        $examplesPath = config_path('workflows/examples');

        if (!File::isDirectory($examplesPath)) {
            $this->command->warn('No config/workflows/examples directory found. Skipping workflow examples.');
            return;
        }

        foreach (File::files($examplesPath) as $file) {
            if (strtolower($file->getExtension()) !== 'json') {
                continue;
            }

            $content = File::get($file->getPathname());
            $data = json_decode($content, true);

            if (json_last_error() !== \JSON_ERROR_NONE || !isset($data['workflow'], $data['steps'])) {
                $this->command->warn("Invalid or incomplete JSON in {$file->getFilename()}. Skipping.");
                continue;
            }

            $this->seedWorkflowFromArray($data);
        }
    }

    protected function seedWorkflowFromArray(array $data): void
    {
        $workflowData = $data['workflow'];
        $key = $workflowData['key'];

        $workflow = Workflow::updateOrCreate(
            ['key' => $key],
            [
                'name' => $workflowData['name'],
                'description' => $workflowData['description'] ?? null,
                'entity_type' => $workflowData['entity_type'] ?? null,
                'version' => $workflowData['version'] ?? '1.0',
                'is_active' => $workflowData['is_active'] ?? true,
                'config' => $workflowData['config'] ?? null,
            ]
        );

        $stepKeys = [];
        foreach ($data['steps'] as $stepData) {
            $step = WorkflowStep::updateOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'key' => $stepData['key'],
                ],
                [
                    'name' => $stepData['name'],
                    'type' => $stepData['type'] ?? 'human',
                    'position' => $stepData['position'] ?? 0,
                    'is_start' => $stepData['is_start'] ?? false,
                    'is_end' => $stepData['is_end'] ?? false,
                    'allowed_roles' => $stepData['allowed_roles'] ?? null,
                    'config' => $stepData['config'] ?? null,
                ]
            );
            $stepKeys[$stepData['key']] = $step->id;
        }

        $transitions = $data['transitions'] ?? [];
        foreach ($transitions as $t) {
            $fromId = $stepKeys[$t['from']] ?? null;
            $toId = $stepKeys[$t['to']] ?? null;
            if (!$fromId || !$toId) {
                continue;
            }

            WorkflowTransition::updateOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'from_step_id' => $fromId,
                    'to_step_id' => $toId,
                ],
                [
                    'name' => $t['name'] ?? null,
                    'condition_key' => $t['condition_key'] ?? null,
                    'condition_expression' => $t['condition_expression'] ?? null,
                    'is_default' => $t['is_default'] ?? false,
                    'is_loopback' => $t['is_loopback'] ?? false,
                    'metadata' => $t['metadata'] ?? null,
                ]
            );
        }

        $this->command->info("Workflow [{$key}] seeded.");
    }
}
