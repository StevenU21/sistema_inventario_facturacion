<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseDetailPolicy
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
        return $this->checkPermission($user, 'update purchases');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update purchases');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'update purchases');
    }
}
