<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductVariantPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read product_variants');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read product_variants');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create product_variants');
    }

    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update product_variants');
    }

    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy product_variants');
    }
}
