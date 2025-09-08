<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class SizePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read sizes');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read sizes');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create sizes');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update sizes');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy sizes');
    }
}
