# Workflow Engine

Generic, configuration-driven workflow engine for Laravel. It supports **human**, **decision**, **auto**, and **waiting** steps, role-based permissions, condition-based transitions, loopbacks, and a full audit trail.

---

## Architecture Overview

| Concept | Description |
|--------|-------------|
| **Workflow** | A reusable flow definition (e.g. `training_program`, `social_aid`). |
| **Workflow Instance** | A running case for a specific entity (e.g. one training application). |
| **Step** | A single stage with a type (`human`, `decision`, `auto`, `waiting`) and optional role assignment. |
| **Transition** | Allowed move from one step to another, with optional `condition_key` and/or `condition_expression`. |

- **Condition evaluator**: Safe expression evaluation (no `eval`). Supports comparisons and `&&` / `||`.
- **Step executors**: Pluggable strategies per step type; auto steps can dispatch events for app-specific actions.
- **Audit**: Every action is logged in `workflow_logs` (step, action, performer, role, timestamp, notes).

---

## Example: Starting a Workflow

```php
use App\Models\Workflow;
use App\Models\DynamicServiceOrder;
use App\Services\Workflow\WorkflowEngine;

// Resolve the engine (injected or app(WorkflowEngine::class))
$engine = app(WorkflowEngine::class);

$workflow = Workflow::where('key', 'training_program')->firstOrFail();
$order = DynamicServiceOrder::find($id);
$user = auth()->user();

$instance = $engine->start($workflow, $order, $user, [
    'source' => 'web',
]);

// Optional: run auto/decision steps from start until a human or waiting step
// (already called inside start())
// $engine->autoRun($instance, $user);
```

---

## Example: Executing a Step (Human Action)

```php
$instance = WorkflowInstance::with('currentStep')->find($id);
$user = auth()->user();

$payload = [
    'condition_key' => 'approved',  // Drives transition selection (e.g. approved / rejected)
    'notes'         => 'Eligibility confirmed.',
    // ... any form data you want stored in instance->data
];

$instance = $engine->executeStep($instance, $user, $payload);
// Engine validates user role vs step's allowed_roles (and optional policy), then
// executes the step, applies transition, and may auto-run following auto/decision steps.
```

---

## Example: Auto-Transition (System)

After a human or decision step, the engine resolves the transition and moves the instance. Auto and decision steps are then run in a loop until a human or waiting step (or end) is reached:

```php
// This is already invoked inside start() and executeStep().
$engine->autoRun($instance, null);
```

To resume from a **waiting** step (e.g. when capacity becomes available), call `executeStep` again with a payload that includes the appropriate `condition_key` (e.g. `retry`), or trigger your own job that calls `executeStep`/`autoRun`.

---

## Example: Finalize or Cancel

```php
$engine->finalize($instance, $user, 'Program completed.');
$engine->cancel($instance, $user, 'Applicant withdrew.');
```

---

## Configuration (JSON Examples)

- **Training program**: `config/workflows/examples/training_program.json`
- **Social aid**: `config/workflows/examples/social_aid.json`

Seed these into the DB:

```bash
php artisan db:seed --class=WorkflowExampleSeeder
```

---

## Roles and Permissions

- Steps define `allowed_roles` (array of role IDs or role titles). Only users with at least one of these roles can execute that step.
- Optional `policy_ability` on a step is checked via Laravel Gate against the instance’s entity (or the instance).
- System-triggered actions (e.g. auto steps) may pass `user: null`; performer in logs will be null.

---

## Auto-step event

When an **auto** step runs, the engine dispatches `App\Events\WorkflowStepAutoExecuted` with `instance`, `step`, and `payload`. Register listeners in `EventServiceProvider` to send notifications, generate documents, or call external APIs. A default listener `HandleWorkflowStepAutoExecuted` logs the event.

---

## Entity relation

Models that can have a workflow instance can use the `HasWorkflowInstance` trait (e.g. `DynamicServiceOrder`). Then:

- `$entity->workflowInstance` – current running/on_hold instance (morphOne)
- `$entity->workflowInstances` – all instances (morphMany)

---

## Admin UI and API (workflow-instances)

Under the `admin` prefix (auth + staff middleware). All actions are gated by `WorkflowInstancePolicy` (viewAny, view, create, update).

**Web (HTML):**

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/admin/workflow-instances` | List instances (index). |
| GET | `/admin/workflow-instances/create` | Form to start a workflow (optional query: `entity_type`, `entity_id`). |
| POST | `/admin/workflow-instances/start` | Start a workflow (form or API). |
| GET | `/admin/workflow-instances/{id}` | Show instance: timeline + execute-step form when current step is human/decision. |
| POST | `/admin/workflow-instances/{id}/execute` | Execute current step (form or API). |

**API (JSON):** Send `Accept: application/json` (or `?format=json` where applicable) to get JSON responses from `show`, `start`, and `execute`.

From the **Beneficiary Order** show page, when the order has a dynamic service order, a card "سير العمل (المحرك الجديد)" shows: either a link to the running workflow instance or "بدء سير عمل جديد" to the create form with entity pre-filled.

---

## Legacy Handling

The existing **dynamic service workflow** implementation (`DynamicServiceWorkflow`, `DynamicServiceWorkflowTransition`, category-specific workflow models and `DynamicServiceWorkflowService`) is **legacy** and must not be extended.

- **Do not** reuse its status-based or if/else flow logic in the new engine.
- **Do not** couple the new Workflow Engine to those models or services.
- Existing data can remain **read-only**; new flows should be created and run only through the generic engine (workflows, workflow_steps, workflow_transitions, workflow_instances, workflow_logs).
- When migrating a given flow type to the new engine:
  1. Define the workflow in the new tables (or via JSON + seeder).
  2. Start new cases with `WorkflowEngine::start()` for the target entity.
  3. Optionally add a `workflowInstance()` morphOne relationship on the entity to link to the new engine’s instance.
  4. Leave legacy workflow tables and code in place until all usages are migrated; then deprecate or remove.

This keeps the new engine generic and configuration-driven while preserving existing data and allowing a clear migration path.
