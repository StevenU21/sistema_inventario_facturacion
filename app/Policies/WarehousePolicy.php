<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read warehouses');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read warehouses');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create warehouses');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update warehouses');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy warehouses');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export warehouses');
    }
}
