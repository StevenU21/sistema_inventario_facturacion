<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Traits\HasPermissionCheck;

class AuditPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read audits');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read audits');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export audits');
    }
}
