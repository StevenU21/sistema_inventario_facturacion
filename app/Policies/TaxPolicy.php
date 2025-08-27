<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read taxes');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read taxes');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create taxes');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update taxes');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy taxes');
    }
}
