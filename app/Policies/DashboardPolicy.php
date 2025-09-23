<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read dashboard');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read dashboard');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create dashboard');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export dashboard');
    }
}
