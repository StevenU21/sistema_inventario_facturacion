<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Traits\HasPermissionCheck;

class PermissionPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read permissions');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'assign permissions');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'revoke permissions');
    }
}