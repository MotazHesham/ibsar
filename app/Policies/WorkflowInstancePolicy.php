<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowInstance;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkflowInstancePolicy
{
    use HandlesAuthorization;

    /**
     * Whether the user can view any workflow instances (list).
     */
    public function viewAny(User $user): bool
    {
        return $user->roles()->exists();
    }

    /**
     * Whether the user can view this workflow instance.
     */
    public function view(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->roles()->exists();
    }

    /**
     * Whether the user can start a new workflow (create instance).
     */
    public function create(User $user): bool
    {
        return $user->roles()->exists();
    }

    /**
     * Whether the user can execute a step on this instance.
     * Step-level role checks are enforced by the WorkflowEngine.
     */
    public function update(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->roles()->exists();
    }
}
