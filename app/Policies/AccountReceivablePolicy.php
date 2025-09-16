<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountReceivablePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        // Reutilizamos permisos de pagos/ventas a crédito
        return $this->checkPermission($user, 'read payments');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read payments');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export payments');
    }
}
