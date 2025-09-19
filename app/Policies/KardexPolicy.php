<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class KardexPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read kardex');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read kardex');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create kardex');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update kardex');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy kardex');
    }

    public function export(User $user): bool
    {
        return $this->checkPermission($user, 'export kardex');
    }
}
