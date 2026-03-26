<?php

namespace App\Policies;

use App\Models\AutofillWorkflow;
use App\Models\User;

class AutofillPolicy
{
    /**
     * Determine if the user can view autofill workflows.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminRights();
    }

    /**
     * Determine if the user can view a specific workflow.
     */
    public function view(User $user, AutofillWorkflow $workflow): bool
    {
        return $user->hasAdminRights();
    }

    /**
     * Determine if the user can create autofill workflows.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminRights();
    }

    /**
     * Determine if the user can manage (modify/abandon/resume) a workflow.
     * Only the creator or a super-admin can manage.
     */
    public function manage(User $user, AutofillWorkflow $workflow): bool
    {
        return $user->id === $workflow->admin_id || $user->isSuperAdmin();
    }

    /**
     * Determine if the user can delete a workflow.
     * Only super-admins can delete.
     */
    public function delete(User $user, AutofillWorkflow $workflow): bool
    {
        return $user->isSuperAdmin();
    }
}
