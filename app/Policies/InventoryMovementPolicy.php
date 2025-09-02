<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryMovementPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read inventory_movements');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read inventory_movements');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export inventory_movements');
    }
}
