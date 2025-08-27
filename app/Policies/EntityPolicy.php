<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read entities');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read entities');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create entities');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update entities');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy entities');
    }
}
