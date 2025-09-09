<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read purchases');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read purchases');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create purchases');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update purchases');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy purchases');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export purchases');
    }
}
