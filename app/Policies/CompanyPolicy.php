<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read companies');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read companies');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create companies');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update companies');
    }
}
