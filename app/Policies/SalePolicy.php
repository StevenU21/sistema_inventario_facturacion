<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read sales');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read sales');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create sales');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update sales');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export sales');
    }
}
