<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Workflow\ExecuteWorkflowStepAction;
use App\Actions\Workflow\StartWorkflowForEntity;
use App\Http\Controllers\Controller;
use App\Models\Workflow;
use App\Models\WorkflowInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller for the generic Workflow Engine (workflow_instances).
 *
 * Does not replace or extend the legacy DynamicServiceWorkflowController.
 */
class WorkflowInstancesController extends Controller
{
    /**
     * List workflow instances (optional index).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', WorkflowInstance::class);

        $instances = WorkflowInstance::with(['workflow', 'currentStep', 'entity'])
            ->latest()
            ->paginate(15);

        return view('admin.workflow-instances.index', compact('instances'));
    }

    /**
     * Show form to start a new workflow (optional entity_type, entity_id in query).
     */
    public function create(Request $request)
    {
        Gate::authorize('create', WorkflowInstance::class);

        $workflows = Workflow::where('is_active', true)->orderBy('name')->get();
        $entityType = $request->query('entity_type', '');
        $entityId = $request->query('entity_id', '');

        return view('admin.workflow-instances.create', compact('workflows', 'entityType', 'entityId'));
    }

    /**
     * Start a new workflow for an entity.
     *
     * Expects: workflow_id, entity_type, entity_id, and optional initial data.
     */
    public function start(Request $request, StartWorkflowForEntity $action)
    {
        Gate::authorize('create', WorkflowInstance::class);

        $request->validate([
            'workflow_id' => 'required|exists:workflows,id',
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $workflow = Workflow::findOrFail($request->input('workflow_id'));
        $entity = $this->resolveEntity($request->input('entity_type'), $request->input('entity_id'));

        if (! $entity) {
            return response()->json(['message' => 'Entity not found.'], Response::HTTP_NOT_FOUND);
        }

        $initialData = $request->except(['workflow_id', 'entity_type', 'entity_id', '_token']);
        try {
            $instance = $action->run($workflow, $entity, $request->user(), $initialData);
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Workflow started.',
                'workflow_instance_id' => $instance->id,
                'current_step' => $instance->currentStep ? [
                    'id' => $instance->currentStep->id,
                    'key' => $instance->currentStep->key,
                    'name' => $instance->currentStep->name,
                    'type' => $instance->currentStep->type,
                ] : null,
            ], Response::HTTP_CREATED);
        }

        return redirect()->route('admin.workflow-instances.show', $instance)
            ->with('status', __('Workflow started.'));
    }

    /**
     * Execute the current step (e.g. approve, reject, submit).
     *
     * Expects: condition_key (required when step has multiple outcomes), notes, and any payload.
     */
    public function executeStep(Request $request, WorkflowInstance $workflowInstance, ExecuteWorkflowStepAction $action)
    {
        Gate::authorize('update', $workflowInstance);

        $request->validate([
            'condition_key' => 'nullable|string|max:64',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payload = array_merge(
            $request->only(['condition_key', 'notes']),
            $request->except(['condition_key', 'notes', '_token'])
        );
        $payload = array_filter($payload, fn ($v) => $v !== null && $v !== '');

        try {
            $instance = $action->run($workflowInstance, $request->user(), $payload);
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Step executed.',
                'workflow_instance_id' => $instance->id,
                'status' => $instance->status,
                'current_step' => $instance->currentStep ? [
                    'id' => $instance->currentStep->id,
                    'key' => $instance->currentStep->key,
                    'name' => $instance->currentStep->name,
                    'type' => $instance->currentStep->type,
                ] : null,
            ]);
        }

        return redirect()->route('admin.workflow-instances.show', $instance)
            ->with('status', __('Step executed.'));
    }

    /**
     * Show workflow instance with timeline (logs). Returns HTML view or JSON.
     */
    public function show(Request $request, WorkflowInstance $workflowInstance)
    {
        Gate::authorize('view', $workflowInstance);

        $workflowInstance->load(['workflow', 'workflow.steps', 'currentStep', 'entity', 'logs' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(100)]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $workflowInstance->id,
                'workflow' => [
                    'id' => $workflowInstance->workflow->id,
                    'key' => $workflowInstance->workflow->key,
                    'name' => $workflowInstance->workflow->name,
                ],
                'entity_type' => $workflowInstance->entity_type,
                'entity_id' => $workflowInstance->entity_id,
                'status' => $workflowInstance->status,
                'current_step' => $workflowInstance->currentStep ? [
                    'id' => $workflowInstance->currentStep->id,
                    'key' => $workflowInstance->currentStep->key,
                    'name' => $workflowInstance->currentStep->name,
                    'type' => $workflowInstance->currentStep->type,
                ] : null,
                'timeline' => $workflowInstance->logs->map(fn ($log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'step_id' => $log->step_id,
                    'from_step_id' => $log->from_step_id,
                    'to_step_id' => $log->to_step_id,
                    'performer_role' => $log->performer_role,
                    'notes' => $log->notes,
                    'created_at' => $log->created_at?->format('c'),
                ]),
            ]);
        }

        $outgoingTransitions = $workflowInstance->currentStep
            ? $workflowInstance->currentStep->outgoingTransitions()->with('toStep')->get()
            : collect();

        return view('admin.workflow-instances.show', compact('workflowInstance', 'outgoingTransitions'));
    }

    protected function resolveEntity(string $type, int $id): ?object
    {
        if (! class_exists($type) || ! is_subclass_of($type, \Illuminate\Database\Eloquent\Model::class)) {
            return null;
        }

        return $type::find($id);
    }
}
