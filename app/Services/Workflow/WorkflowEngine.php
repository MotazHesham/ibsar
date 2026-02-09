<?php

namespace App\Services\Workflow;

use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowLog;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * WorkflowEngine
 *
 * Generic, configuration-driven workflow engine.
 *
 * Responsibilities:
 *  - Start workflows for arbitrary entities.
 *  - Execute steps using pluggable strategies.
 *  - Enforce role-based permissions and optional policies.
 *  - Resolve transitions and move instances forward (including loopbacks).
 *  - Auto-execute system steps (auto, decision) when appropriate.
 *  - Record a full audit trail in workflow_logs.
 *
 * This engine MUST NOT contain any module-specific rules. All domain-specific
 * logic must live in higher-level services, listeners, or jobs.
 */
class WorkflowEngine
{
    public function __construct(
        protected StepExecutorRegistry $executorRegistry,
        protected TransitionResolver $transitionResolver,
    ) {
    }

    /**
     * Start a workflow instance for a given entity.
     */
    public function start(Workflow $workflow, Model $entity, ?User $user = null, array $data = []): WorkflowInstance
    {
        if (!$workflow->is_active) {
            throw new InvalidArgumentException("Workflow [{$workflow->key}] is not active.");
        }

        $startStep = $workflow->steps()->where('is_start', true)->first();
        if (!$startStep) {
            // Fallback to the first step by position if no explicit start step.
            $startStep = $workflow->steps()->orderBy('position')->first();
        }

        if (!$startStep) {
            throw new InvalidArgumentException("Workflow [{$workflow->key}] has no defined steps.");
        }

        return DB::transaction(function () use ($workflow, $entity, $user, $startStep, $data) {
            $instance = new WorkflowInstance([
                'workflow_id' => $workflow->id,
                'entity_type' => $entity->getMorphClass(),
                'entity_id' => $entity->getKey(),
                'current_step_id' => $startStep->id,
                'status' => 'running',
                'started_by_id' => $user?->id,
                'data' => $data,
            ]);
            $instance->save();

            $this->logAction(
                instance: $instance,
                step: $startStep,
                action: 'started',
                user: $user,
                fromStep: null,
                toStep: $startStep,
                notes: null,
                metadata: []
            );

            // Optionally auto-run system steps from the start.
            $this->autoRun($instance, $user);

            return $instance->fresh(['currentStep', 'workflow']);
        });
    }

    /**
     * Execute the current step for the given instance.
     *
     * @param WorkflowInstance $instance
     * @param User|null        $user
     * @param array            $payload     Arbitrary payload (e.g. form data, notes, condition_key).
     */
    public function executeStep(
        WorkflowInstance $instance,
        ?User $user = null,
        array $payload = []
    ): WorkflowInstance {
        $instance->loadMissing(['workflow', 'currentStep']);

        if ($instance->status !== 'running' && $instance->status !== 'on_hold') {
            throw new InvalidArgumentException('Only running or on_hold workflow instances can execute steps.');
        }

        $step = $instance->currentStep;
        if (!$step) {
            throw new InvalidArgumentException('Workflow instance has no current step.');
        }

        $this->assertUserCanExecuteStep($instance, $step, $user);

        return DB::transaction(function () use ($instance, $step, $user, $payload) {
            $executor = $this->executorRegistry->forType($step->type);

            $result = $executor->execute($instance, $step, $user, $payload);

            // Merge data updates into the instance's data JSON.
            if (!empty($result->dataUpdates)) {
                $instance->data = $this->mergeInstanceData($instance->data ?? [], $result->dataUpdates);
                $instance->save();
            }

            // Log execution of this step.
            $this->logAction(
                instance: $instance,
                step: $step,
                action: 'step_executed',
                user: $user,
                fromStep: $step,
                toStep: $step,
                notes: $result->notes,
                metadata: $result->metadata
            );

            // For waiting steps we intentionally stop processing here.
            if ($result->stopProcessing) {
                $instance->status = 'on_hold';
                $instance->save();

                return $instance->fresh(['currentStep']);
            }

            // Resolve and apply transition.
            $this->applyTransitionFromResult($instance, $step, $result, $user);

            // After manual execution, we may chain auto/decision steps.
            $this->autoRun($instance, $user);

            return $instance->fresh(['currentStep']);
        });
    }

    /**
     * Automatically execute system steps from the current step forward
     * as long as they are auto/decision types that do not require human
     * interaction.
     */
    public function autoRun(WorkflowInstance $instance, ?User $systemUser = null): void
    {
        $instance->refresh()->loadMissing(['workflow', 'currentStep']);

        while ($instance->currentStep && in_array($instance->currentStep->type, ['auto', 'decision'], true)) {
            $step = $instance->currentStep;
            $executor = $this->executorRegistry->forType($step->type);

            $result = $executor->execute($instance, $step, $systemUser, []);

            // Apply data updates if any.
            if (!empty($result->dataUpdates)) {
                $instance->data = $this->mergeInstanceData($instance->data ?? [], $result->dataUpdates);
                $instance->save();
            }

            // Log auto-execution.
            $this->logAction(
                instance: $instance,
                step: $step,
                action: 'step_auto_executed',
                user: $systemUser,
                fromStep: $step,
                toStep: $step,
                notes: $result->notes,
                metadata: $result->metadata
            );

            if ($result->stopProcessing) {
                $instance->status = 'on_hold';
                $instance->save();
                break;
            }

            $this->applyTransitionFromResult($instance, $step, $result, $systemUser);

            $instance->refresh()->loadMissing('currentStep');

            // Stop if we reached an end step or the instance is no longer running.
            if (!$instance->currentStep || $instance->status !== 'running') {
                break;
            }
        }
    }

    /**
     * Mark the workflow instance as completed.
     */
    public function finalize(WorkflowInstance $instance, ?User $user = null, ?string $notes = null): WorkflowInstance
    {
        return DB::transaction(function () use ($instance, $user, $notes) {
            $instance->status = 'completed';
            $instance->finished_by_id = $user?->id;
            $instance->current_step_id = null;
            $instance->save();

            $this->logAction(
                instance: $instance,
                step: null,
                action: 'workflow_completed',
                user: $user,
                fromStep: null,
                toStep: null,
                notes: $notes,
                metadata: []
            );

            return $instance;
        });
    }

    /**
     * Cancel a workflow instance (soft-termination).
     */
    public function cancel(WorkflowInstance $instance, ?User $user = null, ?string $notes = null): WorkflowInstance
    {
        return DB::transaction(function () use ($instance, $user, $notes) {
            $instance->status = 'cancelled';
            $instance->finished_by_id = $user?->id;
            $instance->current_step_id = null;
            $instance->save();

            $this->logAction(
                instance: $instance,
                step: null,
                action: 'workflow_cancelled',
                user: $user,
                fromStep: null,
                toStep: null,
                notes: $notes,
                metadata: []
            );

            return $instance;
        });
    }

    /**
     * Apply the transition suggested by the step execution result.
     */
    protected function applyTransitionFromResult(
        WorkflowInstance $instance,
        WorkflowStep $currentStep,
        WorkflowStepExecutionResult $result,
        ?User $user = null
    ): void {
        // If current step is terminal, finalize the workflow.
        if ($currentStep->is_end) {
            $this->finalize($instance, $user);
            return;
        }

        $entityArray = $instance->entity ? $instance->entity->toArray() : [];
        $context = array_merge($entityArray, $instance->data ?? []);

        $transition = $this->transitionResolver->resolve(
            instance: $instance,
            currentStep: $currentStep,
            conditionKey: $result->conditionKey,
            context: $context
        );

        if (!$transition) {
            // No transition found; keep the instance on the same step.
            return;
        }

        $toStep = $transition->toStep;

        $instance->current_step_id = $toStep->id;
        $instance->status = 'running';
        $instance->save();

        $this->logAction(
            instance: $instance,
            step: $toStep,
            action: 'transitioned',
            user: $user,
            fromStep: $currentStep,
            toStep: $toStep,
            notes: $result->notes,
            metadata: [
                'transition_id' => $transition->id,
                'condition_key' => $result->conditionKey,
                'is_loopback' => $transition->is_loopback,
            ]
        );

        // If the destination step is terminal, immediately finalize.
        if ($toStep->is_end) {
            $this->finalize($instance, $user);
        }
    }

    /**
     * Ensure the given user has permission to execute the step.
     */
    protected function assertUserCanExecuteStep(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?User $user = null
    ): void {
        // System-initiated actions may skip user checks.
        if (!$user) {
            return;
        }

        // Role-based check from step.allowed_roles.
        $allowedRoles = $step->allowed_roles ?? [];
        if (!empty($allowedRoles)) {
            $allowedRoles = array_map('strval', $allowedRoles);

            $userRoleIds = $user->roles->pluck('id')->map(fn ($id) => (string) $id)->all();
            $userRoleTitles = $user->roles->pluck('title')->map(fn ($title) => (string) $title)->all();

            $userRoleIdentifiers = array_unique(array_merge($userRoleIds, $userRoleTitles));

            $intersect = array_intersect($allowedRoles, $userRoleIdentifiers);

            if (empty($intersect)) {
                throw new InvalidArgumentException('User is not allowed to execute this workflow step (role mismatch).');
            }
        }

        // Optional policy guard, using the related entity as subject if available.
        if ($step->policy_ability) {
            $subject = $instance->entity ?? $instance;
            Gate::forUser($user)->authorize($step->policy_ability, $subject);
        }
    }

    protected function mergeInstanceData(array $original, array $updates): array
    {
        return Arr::dot($original) === [] && Arr::dot($updates) === []
            ? $updates
            : array_replace_recursive($original, $updates);
    }

    /**
     * Record an audit log entry.
     */
    protected function logAction(
        WorkflowInstance $instance,
        ?WorkflowStep $step,
        string $action,
        ?User $user,
        ?WorkflowStep $fromStep,
        ?WorkflowStep $toStep,
        ?string $notes,
        array $metadata
    ): void {
        $log = new WorkflowLog([
            'workflow_instance_id' => $instance->id,
            'workflow_id' => $instance->workflow_id,
            'step_id' => $step?->id,
            'from_step_id' => $fromStep?->id,
            'to_step_id' => $toStep?->id,
            'action' => $action,
            'performer_type' => $user?->getMorphClass(),
            'performer_id' => $user?->getKey(),
            'performer_role' => $user ? $user->roles()->pluck('title')->implode(',') : null,
            'notes' => $notes,
            'metadata' => $metadata,
        ]);

        $log->save();
    }
}

