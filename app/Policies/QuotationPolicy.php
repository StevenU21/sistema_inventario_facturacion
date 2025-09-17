<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotationPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read quotations');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read quotations');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create quotations');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update quotations');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy quotations');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export quotations');
    }
}
