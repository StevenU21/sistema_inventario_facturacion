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

    public function create_clients(User $user): bool
    {
        return $this->checkPermission($user, 'create clients');
    }

    public function update_clients(User $user): bool
    {
        return $this->checkPermission($user, 'update clients');
    }

    public function create_suppliers(User $user): bool
    {
        return $this->checkPermission($user, 'create suppliers');
    }

    public function update_suppliers(User $user): bool
    {
        return $this->checkPermission($user, 'update suppliers');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy entities');
    }
}
